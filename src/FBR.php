<?php
namespace FBR;

class FBR
{
    const CMD_UPLOAD     = 32;
    const CMD_STATUS     = 64;
    const CMD_GET_USTATS = 80;
    const CMD_GET_RSTATS = 128;
    const CMD_GET_FACES  = 129;

    const ANS_OK         = 33;
    const ANS_PROCESSING = 65;
    const ANS_ERROR      = 66;
    const ANS_COMPLETED  = 67;
    const ANS_GET_USTATS = 80;
    const ANS_GET_RSTATS = 128;
    const ANS_GET_FACES  = 129;

    /**
     * @var string
     */
    private $_url;

    /**
     * @var string
     */
    private $_client_id;

    /**
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    private $_cache = null;

    /**
     * @var integer
     */
    private $_ttl = 3600;

    /**
     * @var \WildWolf\CurlWrapperInterface
     */
    private $_curl = null;

    /**
     * @param string $url
     * @param string $client_id
     * @throws \InvalidArgumentException
     */
    public function __construct(string $url, string $client_id = '')
    {
        if (!$url) {
            throw new \InvalidArgumentException();
        }

        $this->_url       = $url;
        $this->_client_id = $client_id;
    }

    /**
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     */
    public function setCache(\Psr\Cache\CacheItemPoolInterface $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * @param \WildWolf\CurlWrapperInterface $w
     */
    public function setCurlWrapper(\WildWolf\CurlWrapperInterface $w)
    {
        $this->_curl = $w;
    }

    /**
     * @return string
     */
    public function clientId() : string
    {
        return $this->_client_id;
    }

    /**
     * @param string $client_id
     * @return \FBR\FBR
     */
    public function setClientId(string $client_id) : \FBR\FBR
    {
        $this->_client_id = $client_id;
        return $this;
    }

    /**
     * @return int
     */
    public function ttl() : int
    {
        return $this->_ttl;
    }

    /**
     * @param int $ttl
     * @return \FBR\FBR
     */
    public function setTtl(int $ttl) : \FBR\FBR
    {
        $this->_ttl = $ttl;
        return $this;
    }

    /**
     * @return string
     */
    private static function guidv4() : string
    {
        $data    = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * @param array $request
     * @return string|object
     */
    private function sendRequest(array $request)
    {
        $request['signature']          = '';
        $request['data']['client_id']  = $this->_client_id;
        $request['data']['reqID_clnt'] = self::guidv4();
        $request['data']['datetime']   = date('d.m.Y H:i:s');

        $request = json_encode($request);

        if (!$this->_curl) {
            $this->_curl = new \WildWolf\CurlWrapper();
        }

        $this->_curl->reset();
        $this->_curl->setOptions([
            CURLOPT_URL            => $this->_url,
            CURLOPT_HEADER         => false,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Expect:', 'Content-Type: text/json', 'Content-Length:'],
            CURLOPT_POSTFIELDS     => dechex(strlen($request)) . "\r\n" . $request . "\r\n0\r\n\r\n",
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = $this->_curl->execute();
        $code     = $this->_curl->info(CURLINFO_HTTP_CODE);
        return (200 === $code) ? json_decode($response) : $response;
    }

    /**
     * @param resource|\Imagick|string $r
     * @throws \InvalidArgumentException
     * @return string|object
     */
    public function uploadFile($r)
    {
        $data = null;
        if (is_resource($r)) {
            switch (get_resource_type($r)) {
                case 'stream':
                    $data = base64_encode(stream_get_contents($r));
                    break;

                case 'gd':
                    ob_start();
                    imagejpeg($r);
                    $data = base64_encode(ob_get_clean());
                    break;
            }
        }
        elseif (is_string($r)) {
            $data = base64_encode($r);
        }
        elseif (is_object($r) && $r instanceof \Imagick) {
            $r->setImageFormat("jpeg");
            $data = base64_encode($r->getImageBlob());
        }

        if (!$data) {
            throw new \InvalidArgumentException();
        }

        $request = [
            'req_type'  => self::CMD_UPLOAD,
            'data'      => [
                'reqID_serv'   => '',
                'segment'      => '',
                'foto'         => $data,
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->sendRequest($request);
    }

    /**
     * @param string $guid
     * @return string|object
     */
    public function checkUploadStatus(string $guid)
    {
        $item = null;
        if ($this->_cache) {
            $key  = self::CMD_STATUS . '_' . $guid;
            $item = $this->_cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $request = [
            'req_type'  => self::CMD_STATUS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => null,
                'foto'         => null,
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        $result = $this->sendRequest($request);
        if ($item && is_object($result) && $result->ans_type != self::ANS_PROCESSING) {
            $item->set($result);
            $item->expiresAfter($this->_ttl);
            $this->_cache->save($item);
        }

        return $result;
    }

    /**
     * @param string $guid
     * @return string|object
     */
    public function getUploadStats(string $guid)
    {
        $item = null;
        if ($this->_cache) {
            $key  = self::CMD_GET_USTATS . '_' . $guid;
            $item = $this->_cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $request = [
            'req_type'  => self::CMD_GET_USTATS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => null,
                'foto'         => null,
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ]
        ];

        $result = $this->sendRequest($request);
        if ($item && is_object($result)) {
            $item->set($result);
            $item->expiresAfter($this->_ttl);
            $this->_cache->save($item);
        }

        return $result;
    }

    /**
     * @param string $guid
     * @param int $n
     * @return string|object
     */
    public function getRecognitionStats(string $guid, int $n)
    {
        $item = null;
        if ($this->_cache) {
            $key  = self::CMD_GET_RSTATS . '_' . $guid . '_' . $n;
            $item = $this->_cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $request = [
            'req_type'  => self::CMD_GET_RSTATS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => null,
                'foto'         => null,
                'ResultNumber' => $n,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ]
        ];

        $result = $this->sendRequest($request);
        if ($item && is_object($result)) {
            $item->set($result);
            $item->expiresAfter($this->_ttl);
            $this->_cache->save($item);
        }

        return $result;
    }

    /**
     * @param string $guid
     * @param int $n
     * @param int $count
     * @return string|object
     */
    public function getFaces(string $guid, int $n, int $count = 20)
    {
        $item = null;
        if ($this->_cache) {
            $key  = self::CMD_GET_FACES . '_' . $guid . '_' . $n . '_' . $count;
            $item = $this->_cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $request = [
            'req_type'  => self::CMD_GET_FACES,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => null,
                'foto'         => null,
                'ResultNumber' => $n,
                'par1'         => 0,
                'par2'         => $count,
                'comment'      => '',
            ]
        ];

        $result = $this->sendRequest($request);
        if ($item && is_object($result)) {
            $item->set($result);
            $item->expiresAfter($this->_ttl);
            $this->_cache->save($item);
        }

        return $result;
    }
}

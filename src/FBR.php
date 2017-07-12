<?php
namespace FBR;

class FBR
{
    private const CMD_UPLOAD     = 32;
    private const CMD_STATUS     = 64;
    private const CMD_GET_FACES  = 129;

    public const ANS_OK          = 33;
    public const ANS_PROCESSING  = 65;
    public const ANS_ERROR       = 66;
    public const ANS_COMPLETED   = 67;
    public const ANS_GET_FACES   = 129;

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

        $ch = curl_init($this->_url);
        curl_setopt_array($ch, [
            CURLOPT_HEADER         => false,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Expect:', 'Content-Type: text/json', 'Content-Length:'],
            CURLOPT_POSTFIELDS     => dechex(strlen($request)) . "\r\n" . $request . "\r\n0\r\n\r\n",
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
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

<?php

namespace WildWolf\FBR;

use WildWolf\FBR\ResponseFactory;
use WildWolf\FBR\Response\Base;
use WildWolf\FBR\Response\InProgress;

class Client
{
    const CMD_UPLOAD     = 32;
    const CMD_STATUS     = 64;
    const CMD_GET_USTATS = 80;
    const CMD_GET_RSTATS = 128;
    const CMD_GET_FACES  = 129;

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
     * @return \WildWolf\FBR\Client
     */
    public function setClientId(string $client_id) : \WildWolf\FBR\Client
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
     * @return \WildWolf\FBR\Client
     */
    public function setTtl(int $ttl) : \WildWolf\FBR\Client
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

    private function prepareCurl(string $request)
    {
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
    }

    /**
     * @param array $request
     * @throws \WildWolf\FBR\Exception
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    private function sendRequest(array $request)
    {
        $request['signature']          = '';
        $request['data']['client_id']  = $this->_client_id;
        $request['data']['reqID_clnt'] = self::guidv4();
        $request['data']['datetime']   = date('d.m.Y H:i:s');

        $request = json_encode($request);

        $this->prepareCurl($request);

        $response = $this->_curl->execute();
        $code     = $this->_curl->info(CURLINFO_HTTP_CODE);

        if ($code === 200) {
            $obj = json_decode($response);
            if (isset($obj->ans_type)) {
                return ResponseFactory::create($obj);
            }
        }

        throw new Exception($response, $code);
    }

    private static function resourceToBase64($r)
    {
        switch (get_resource_type($r)) {
            case 'stream':
                return base64_encode(stream_get_contents($r));

            case 'gd':
                ob_start();
                imagejpeg($r);
                return base64_encode(ob_get_clean());
        }

        return null;
    }

    private static function prepareFile($r)
    {
        if (is_resource($r)) {
            return self::resourceToBase64($r);
        }

        if (is_string($r)) {
            return base64_encode($r);
        }

        if ($r instanceof \Imagick) {
            $r->setImageFormat('jpeg');
            return base64_encode($r->getImageBlob());
        }

        return null;
    }

    /**
     * @param resource|\Imagick|string $r
     * @throws \InvalidArgumentException
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function uploadFile($r)
    {
        $data = self::prepareFile($r);
        if (empty($data)) {
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

    private function maybeSendRequest(string $key, array $request)
    {
        $item = null;
        if ($this->_cache) {
            $item = $this->_cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $result = $this->sendRequest($request);

        if ($item && $result->cacheable()) {
            $item->set($result)->expiresAfter($this->_ttl);
            $this->_cache->save($item);
        }

        return $result;
    }

    /**
     * @param string $guid
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function checkUploadStatus(string $guid)
    {
        $key     = self::CMD_STATUS . '_' . $guid;
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

        return $this->maybeSendRequest($key, $request);
    }

    /**
     * @param string $guid
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function getUploadStats(string $guid)
    {
        $key     = self::CMD_GET_USTATS . '_' . $guid;
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

        return $this->maybeSendRequest($key, $request);
    }

    /**
     * @param string $guid
     * @param int $n
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function getRecognitionStats(string $guid, int $n)
    {
        $key     = self::CMD_GET_RSTATS . '_' . $guid . '_' . $n;
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

        return $this->maybeSendRequest($key, $request);
    }

    /**
     * @param string $guid
     * @param int $n
     * @param int $count
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function getFaces(string $guid, int $n, int $offset, int $count = 20)
    {
        $key     = self::CMD_GET_FACES . '_' . $guid . '_' . $n . '_' . $offset . '_' . $count;
        $request = [
            'req_type'  => self::CMD_GET_FACES,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => null,
                'foto'         => null,
                'ResultNumber' => $n,
                'par1'         => $offset,
                'par2'         => $count,
                'comment'      => '',
            ]
        ];

        return $this->maybeSendRequest($key, $request);
    }
}

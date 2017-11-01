<?php

namespace WildWolf\FBR;

use WildWolf\FBR\Response\Base;
use WildWolf\FBR\Response\InProgress;

class Client extends ClientBase
{
    const CMD_GET_DBSTATS = 8;
    const CMD_START_CMP   = 16;
    const CMD_UPLOAD_REF  = 17;
    const CMD_CMP_RESULTS = 18;
    const CMD_UPLOAD      = 32;
    const CMD_STATUS      = 64;
    const CMD_GET_USTATS  = 80;
    const CMD_GET_RSTATS  = 128;
    const CMD_GET_FACES   = 129;

    /**
     * @param resource|\Imagick|string $r
     * @param int $priority
     * @param mixed $segment
     * @param string $comment
     * @throws \InvalidArgumentException
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function uploadFile($r, int $priority = 2, $segment = 0, $comment = '')
    {
        static $lut = [0 => 'A', 1 => 'B', 2 => 'C'];

        $data = self::prepareFile($r);
        if (empty($data)) {
            throw new \InvalidArgumentException();
        }

        $request = [
            'req_type'  => self::CMD_UPLOAD,
            'data'      => [
                'reqID_serv'   => '',
                'segment'      => $segment,
                'foto'         => $data,
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => $comment,
            ],
        ];

        $request['data']['client_id'] = $lut[$priority] ?? 'C';
        return $this->sendRequest($request);
    }

    /**
     * @param string $guid
     * * @param mixed $segment
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function checkUploadStatus(string $guid, $segment = 0)
    {
        $key     = self::CMD_STATUS . '_' . $guid . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_STATUS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => $segment,
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
     * @param mixed $segment
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function getUploadStats(string $guid, $segment = 0)
    {
        $key     = self::CMD_GET_USTATS . '_' . $guid . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_GET_USTATS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => $segment,
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
     * @param mixed $segment
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function getRecognitionStats(string $guid, int $n, $segment = 0)
    {
        $key     = self::CMD_GET_RSTATS . '_' . $guid . '_' . $n . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_GET_RSTATS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => $segment,
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
     * @param mixed $segment
     * @return \WildWolf\FBR\Response\UploadAck|\WildWolf\FBR\Response\UploadError|\WildWolf\FBR\Response\InProgress|\WildWolf\FBR\Response\Failed|\WildWolf\FBR\Response\ResultReady|\WildWolf\FBR\Response\Stats|\WildWolf\FBR\Response\MatchStats|\WildWolf\FBR\Response\Match|\WildWolf\FBR\Response\Base
     */
    public function getFaces(string $guid, int $n, int $offset = 0, int $count = 20, $segment = 0)
    {
        $key     = self::CMD_GET_FACES . '_' . $guid . '_' . $n . '_' . $offset . '_' . $count . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_GET_FACES,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => $segment,
                'foto'         => null,
                'ResultNumber' => $n,
                'par1'         => $offset,
                'par2'         => $count,
                'comment'      => '',
            ]
        ];

        return $this->maybeSendRequest($key, $request);
    }

    /**
     * @param resource|\Imagick|string $r
     * @param int $num_photos
     * @param string $comment
     * @throws \InvalidArgumentException
     */
    public function startCompare($r, int $num_photos, string $comment = '')
    {
        $data = self::prepareFile($r);
        if (empty($data)) {
            throw new \InvalidArgumentException();
        }

        $request = [
            'req_type' => self::CMD_START_CMP,
            'data'     => [
                'reqID_serv'   => '',
                'segment'      => 0,
                'foto'         => $data,
                'ResultNumber' => $num_photos,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => $comment
            ]
        ];

        return $this->sendRequest($request);
    }

    public function uploadRefPhoto(string $guid, $r, int $n, int $cnt, string $name)
    {
        $data = self::prepareFile($r);
        if (empty($data) || $n < 1 || $n > $cnt) {
            throw new \InvalidArgumentException();
        }

        $request = [
            'req_type' => self::CMD_UPLOAD_REF,
            'data'     => [
                'reqID_serv'   => $guid,
                'segment'      => 0,
                'foto'         => $data,
                'ResultNumber' => $cnt,
                'par1'         => $n,
                'par2'         => 0,
                'comment'      => $name,
            ]
        ];

        return $this->sendRequest($request);
    }

    public function getComparisonResults(string $guid)
    {
        $key     = self::CMD_CMP_RESULTS . '_' . $guid;
        $request = [
            'req_type' => self::CMD_CMP_RESULTS,
            'data'     => [
                'reqID_serv'   => $guid,
                'segment'      => 0,
                'foto'         => null,
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ]
        ];

        return $this->maybeSendRequest($key, $request);
    }

    public function dbStats()
    {
        $request = [
            'req_type'  => self::CMD_GET_DBSTATS,
            'data'      => [
                'reqID_serv'   => '',
                'segment'      => 0,
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->sendRequest($request);
    }
}

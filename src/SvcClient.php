<?php

namespace WildWolf\FBR;

class SvcClient extends ClientBase
{
    const CMD_QUERY_FACES        = 192;
    const CMD_QF_STATUS          = 193;
    const CMD_QF_RESULT          = 194;
    const CMD_QUERY_FACE_STATS   = 200;
    const CMD_FACE_STATS_RESULTS = 201;
    const CMD_PREPARE_ADD        = 204;
    const CMD_PREPARE_ADD_STATUS = 205;
    const CMD_PREPARE_GET_FACES  = 206;
    const CMD_ADD_FACES          = 207;
    const CMD_DELETE_FACES       = 208;
    const CMD_DELETE_RESULT      = 209;

    protected function encodeRequest(array $request) : string
    {
        return "0\r\n" . parent::encodeRequest($request) . "\r\n0\r\n\r\n";
    }

    public function queryFaces(string $sector, int $segment = 0)
    {
        $request = [
            'req_type'  => self::CMD_QUERY_FACES,
            'data'      => [
                'reqID_serv'   => '',
                'segment'      => $segment,
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => $sector,
            ],
        ];

        return $this->sendRequest($request);
    }

    public function getQFStatus(string $guid, int $segment = 0)
    {
        $key     = self::CMD_QF_STATUS . '_' . $guid . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_QF_STATUS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => $segment,
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->maybeSendRequest($key, $request);
    }

    public function getQFResult(string $guid, int $segment = 0, int $start = 1, int $count = -1)
    {
        $key     = self::CMD_QF_RESULT . '_' . $guid . '_' . $segment . '_' . $start . '_' . $count;
        $request = [
            'req_type'  => self::CMD_QF_RESULT,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => $segment,
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => $start,
                'par2'         => $count,
                'comment'      => '',
            ],
        ];

        return $this->maybeSendRequest($key, $request);
    }

    public function queryFaceStats(int $segment = 0)
    {
        $request = [
            'req_type'  => self::CMD_QUERY_FACE_STATS,
            'data'      => [
                'reqID_serv'   => '',
                'segment'      => $segment,
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->sendRequest($request);
    }

    public function getFaceStatsResult(string $guid, int $segment = 0)
    {
        $key     = self::CMD_FACE_STATS_RESULTS . '_' . $guid . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_FACE_STATS_RESULTS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => $segment,
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->maybeSendRequest($key, $request);
    }

    public function preparePhotoForAddition($r, int $segment, string $sector, string $filename)
    {
        $data = self::prepareFile($r);
        if (empty($data)) {
            throw new \InvalidArgumentException();
        }

        $request = [
            'req_type'  => self::CMD_PREPARE_ADD,
            'data'      => [
                'reqID_serv'   => "",
                'segment'      => $segment,
                'foto'         => $data,
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => $sector . '<' . $filename,
            ],
        ];

        return $this->sendRequest($request);
    }

    public function getPreparationStatus(string $guid)
    {
        $key     = self::CMD_PREPARE_ADD_STATUS . '_' . $guid;
        $request = [
            'req_type'  => self::CMD_PREPARE_ADD_STATUS,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => '',
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->maybeSendRequest($key, $request);
    }

    public function getCapturedFaces(string $guid)
    {
        $key     = self::CMD_PREPARE_GET_FACES . '_' . $guid;
        $request = [
            'req_type'  => self::CMD_PREPARE_GET_FACES,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => '',
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->maybeSendRequest($key, $request);
    }

    public function addFaces(string $guid, array $list = null)
    {
        $list = (null === $list) ? 1 : join('*', $list);

        $request = [
            'req_type'  => self::CMD_ADD_FACES,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => '',
                'foto'         => base64_encode($list),
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->sendRequest($request);
    }

    public function deleteFaces(array $list)
    {
        $list = join("\n", $list);

        $request = [
            'req_type'  => self::CMD_DELETE_FACES,
            'data'      => [
                'reqID_serv'   => '',
                'segment'      => '',
                'foto'         => base64_encode($list),
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->sendRequest($request);
    }

    public function getDeleteResult(string $guid)
    {
        $key     = self::CMD_DELETE_RESULT . '_' . $guid;
        $request = [
            'req_type'  => self::CMD_DELETE_RESULT,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => '',
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->maybeSendRequest($key, $request);
    }
}

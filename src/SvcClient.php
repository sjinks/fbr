<?php

namespace WildWolf\FBR;

class SvcClient extends ClientBase
{
    const CMD_QUERY_FACES = 192;
    const CMD_QF_STATUS   = 193;
    const CMD_QF_RESULT   = 194;

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
}

<?php

namespace WildWolf\FBR;

class SvcClient extends ClientBase
{
    const CMD_BASE_STATUS           =   8;
    const CMD_SECTOR_REQUEST_INIT   = 192;
    const CMD_SECTOR_REQUEST_STATUS = 193;
    const CMD_SECTOR_REQUEST_RESULT = 194;
    const CMD_SECTOR_STATS_INIT     = 200;
    const CMD_SECTOR_STATS_RESULT   = 201;
    const CMD_INSERT_INIT           = 204;
    const CMD_INSERT_STATUS         = 205;
    const CMD_INSERT_GET_FACES      = 206;
    const CMD_INSERT_PROCESS        = 207;
    const CMD_DELETE_INIT           = 208;
    const CMD_DELETE_STATUS         = 209;

    protected function encodeRequest(array $request) : string
    {
        return "0\r\n" . parent::encodeRequest($request) . "\r\n0\r\n\r\n";
    }

    /**
     * @return \WildWolf\FBR\Response\Service\QuerySegmentsResult
     */
    public function querySegments()
    {
        $request = [
            'req_type'  => self::CMD_BASE_STATUS,
            'data'      => [
                'reqID_serv'   => '',
                'segment'      => '',
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => 0,
                'par2'         => 0,
                'comment'      => '',
            ],
        ];

        return $this->sendRequest($request);
    }

    /**
     * @param int $segment
     * @param string $sector
     * @return \WildWolf\FBR\Response\Service\QuerySectorAck
     */
    public function querySector(int $segment, string $sector)
    {
        $request = [
            'req_type'  => self::CMD_SECTOR_REQUEST_INIT,
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

    /**
     * @param string $guid
     * @return \WildWolf\FBR\Response\Service\QuerySectorStatus
     */
    public function getQuerySectorStatus(string $guid)
    {
        $key     = self::CMD_SECTOR_REQUEST_STATUS . '_' . $guid;
        $request = [
            'req_type'  => self::CMD_SECTOR_REQUEST_STATUS,
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

    /**
     * @param string $guid
     * @param int $start
     * @param int $count
     * @return \WildWolf\FBR\Response\Service\QuerySectorResult
     */
    public function getQuerySectorResult(string $guid, int $start = 1, int $count = -1)
    {
        $key     = self::CMD_SECTOR_REQUEST_RESULT . '_' . $guid . '_' . $start . '_' . $count;
        $request = [
            'req_type'  => self::CMD_SECTOR_REQUEST_RESULT,
            'data'      => [
                'reqID_serv'   => $guid,
                'segment'      => '',
                'foto'         => '',
                'ResultNumber' => 0,
                'par1'         => $start,
                'par2'         => $count,
                'comment'      => '',
            ],
        ];

        return $this->maybeSendRequest($key, $request);
    }

    /**
     * @param int $segment
     * @return \WildWolf\FBR\Response\Service\QuerySegmentAck
     */
    public function querySegment(int $segment)
    {
        $request = [
            'req_type'  => self::CMD_SECTOR_STATS_INIT,
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

    /**
     * @param string $guid
     * @return \WildWolf\FBR\Response\Service\QuerySegmentResult
     */
    public function getQuerySegmentResult(string $guid)
    {
        $key     = self::CMD_SECTOR_STATS_RESULT . '_' . $guid;
        $request = [
            'req_type'  => self::CMD_SECTOR_STATS_RESULT,
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

    /**
     * @param string|resource|\Imagick $r
     * @param int $segment
     * @param string $sector
     * @param string $filename
     * @throws \InvalidArgumentException
     * @return \WildWolf\FBR\Response\Service\PrepareAddAck
     */
    public function preparePhotoForAddition($r, int $segment, string $sector, string $filename)
    {
        $data = self::prepareFile($r);
        if (empty($data)) {
            throw new \InvalidArgumentException();
        }

        $request = [
            'req_type'  => self::CMD_INSERT_INIT,
            'data'      => [
                'reqID_serv'   => '',
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

    /**
     * @param string $guid
     * @return \WildWolf\FBR\Response\Service\PrepareAddStatus
     */
    public function getPrepareStatus(string $guid)
    {
        $key     = self::CMD_INSERT_STATUS . '_' . $guid;
        $request = [
            'req_type'  => self::CMD_INSERT_STATUS,
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

    /**
     * @param string $guid
     * @return \WildWolf\FBR\Response\Service\PreparedFaces
     */
    public function getPreparedFaces(string $guid)
    {
        $key     = self::CMD_INSERT_GET_FACES . '_' . $guid;
        $request = [
            'req_type'  => self::CMD_INSERT_GET_FACES,
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

    /**
     * @param string $guid
     * @param array $list
     * @return \WildWolf\FBR\Response\Service\AddFacesAck
     */
    public function addPreparedFaces(string $guid, array $list = null)
    {
        $list = (null === $list) ? -1 : join('*', $list);

        $request = [
            'req_type'  => self::CMD_INSERT_PROCESS,
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

    /**
     * @param array $list
     * @return \WildWolf\FBR\Response\Service\DeleteAck
     */
    public function delete(array $list)
    {
        $list = join("\n", $list);

        $request = [
            'req_type'  => self::CMD_DELETE_INIT,
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

    /**
     * @param string $guid
     * @return \WildWolf\FBR\Response\Service\DeleteStatus
     */
    public function getDeleteStatus(string $guid)
    {
        $key     = self::CMD_DELETE_STATUS . '_' . $guid;
        $request = [
            'req_type'  => self::CMD_DELETE_STATUS,
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

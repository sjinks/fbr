<?php

namespace WildWolf\FBR;

class Client extends ClientBase
{
    const CMD_GET_DBSTATS    = 8;
    const CMD_START_CMP      = 16;
    const CMD_UPLOAD_REF     = 17;
    const CMD_CMP_RESULTS    = 18;
    const CMD_UPLOAD_SRCH    = 32;
    const CMD_SRCH_STATUS    = 64;
    const CMD_CAPTURED_FACES = 80;
    const CMD_RCGN_STATS     = 128;
    const CMD_MATCHED_FACES  = 129;

    /**
     * Initial request to recognize and search for faces.
     * Photo $r is sent to the server, the server acknowledges the upload (SearchUploadAck)
     * or returns an error (SearchUploadError)
     *
     * @param resource|\Imagick|string $r
     * @param int $priority
     * @param mixed $segment
     * @param string $comment
     * @throws \InvalidArgumentException
     * @return \WildWolf\FBR\Response\SearchUploadAck|\WildWolf\FBR\Response\SearchUploadError|\WildWolf\FBR\Response\Base
     */
    public function uploadPhotoForSearch($r, int $priority = 2, $segment = 0, $comment = '')
    {
        static $lut = [0 => 'A', 1 => 'B', 2 => 'C'];

        $data = self::prepareFile($r);
        if (empty($data)) {
            throw new \InvalidArgumentException();
        }

        $request = [
            'req_type'  => self::CMD_UPLOAD_SRCH,
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
     * Queries search status ($guid is data.reqID_serv from SearchUploadAck).
     * Returns SearchInProgress, SearchFailed, or SearchCompleted
     *
     * @param string $guid
     * @param mixed $segment
     * @return \WildWolf\FBR\Response\SearchInProgress|\WildWolf\FBR\Response\SearchFailed|\WildWolf\FBR\Response\SearchCompleted|\WildWolf\FBR\Response\Base
     */
    public function checkSearchStatus(string $guid, $segment = 0)
    {
        $key     = self::CMD_SRCH_STATUS . '_' . $guid . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_SRCH_STATUS,
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
     * Gets captured faces
     *
     * @param string $guid
     * @param mixed $segment
     * @return \WildWolf\FBR\Response\CapturedFaces|\WildWolf\FBR\Response\Base
     */
    public function getCapturedFaces(string $guid, $segment = 0)
    {
        $key     = self::CMD_CAPTURED_FACES. '_' . $guid . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_CAPTURED_FACES,
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
     * Get recognition statistics (number of matches, minimum similarity, maximum similarity)
     *
     * @param string $guid
     * @param int $n
     * @param mixed $segment
     * @return \WildWolf\FBR\Response\RecognitionStats|\WildWolf\FBR\Response\Base
     */
    public function getRecognitionStats(string $guid, int $n, $segment = 0)
    {
        $key     = self::CMD_RCGN_STATS . '_' . $guid . '_' . $n . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_RCGN_STATS,
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
     * Get matched faces
     *
     * @param string $guid
     * @param int $n Result number (1-based)
     * @param int $count
     * @param mixed $segment
     * @return \WildWolf\FBR\Response\MatchedFaces|\WildWolf\FBR\Response\Base
     */
    public function getMatchedFaces(string $guid, int $n, int $offset = 0, int $count = 20, $segment = 0)
    {
        $key     = self::CMD_MATCHED_FACES. '_' . $guid . '_' . $n . '_' . $offset . '_' . $count . '_' . $segment;
        $request = [
            'req_type'  => self::CMD_MATCHED_FACES,
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

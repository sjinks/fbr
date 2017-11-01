<?php

namespace WildWolf\FBR;

use WildWolf\FBR\Response\Base;
use WildWolf\FBR\Response\UploadAck;
use WildWolf\FBR\Response\UploadError;
use WildWolf\FBR\Response\InProgress;
use WildWolf\FBR\Response\Failed;
use WildWolf\FBR\Response\ResultReady;
use WildWolf\FBR\Response\Stats;
use WildWolf\FBR\Response\MatchStats;
use WildWolf\FBR\Response\Match;
use WildWolf\FBR\Response\StartCompareAck;
use WildWolf\FBR\Response\UploadCompareAck;
use WildWolf\FBR\Response\CompareCompleted;
use WildWolf\FBR\Response\DBStats;
use WildWolf\FBR\Response\QueryFacesAck;
use WildWolf\FBR\Response\QueryFacesStatus;
use WildWolf\FBR\Response\QueryFacesResult;
use WildWolf\FBR\Response\QueryFaceStatsAck;
use WildWolf\FBR\Response\QueryFaceStatsResult;

class ResponseFactory
{
    public static function create(\stdClass $data)
    {
        switch ($data->ans_type) {
            case Base::TYPE_GET_DBSTATS:    return new DBStats($data);
            case Base::TYPE_START_CMP_ACK:  return new StartCompareAck($data);
            case Base::TYPE_UPLOAD_CMP_ACK: return new UploadCompareAck($data);
            case Base::TYPE_CMP_COMPLETED:  return new CompareCompleted($data);
            case Base::TYPE_UPLOAD_ACK:     return new UploadAck($data);
            case Base::TYPE_UPLOAD_ERR:     return new UploadError($data);
            case Base::TYPE_IN_PROGRESS:    return new InProgress($data);
            case Base::TYPE_FAILED:         return new Failed($data);
            case Base::TYPE_COMPLETED:      return new ResultReady($data);
            case Base::TYPE_GET_RSTATS:     return new Stats($data);
            case Base::TYPE_GET_MSTATS:     return new MatchStats($data);
            case Base::TYPE_GET_FACES:      return new Match($data);
            case Base::TYPE_QUERY_FACES:    return new QueryFacesAck($data);
            case Base::TYPE_QF_STATUS:      return new QueryFacesStatus($data);
            case Base::TYPE_QF_RESULT:      return new QueryFacesResult($data);
            case Base::TYPE_GET_FSTATS:     return new QueryFaceStatsAck($data);
            case Base::TYPE_FSTATS_RESULT:  return new QueryFaceStatsResult($data);
            default:                        return new Base($data);
        }
    }
}

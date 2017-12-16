<?php

namespace WildWolf\FBR;

use WildWolf\FBR\Response\Base;
use WildWolf\FBR\Response\SearchUploadAck;
use WildWolf\FBR\Response\SearchUploadError;
use WildWolf\FBR\Response\SearchInProgress;
use WildWolf\FBR\Response\SearchFailed;
use WildWolf\FBR\Response\SearchCompleted;
use WildWolf\FBR\Response\CapturedFaces;
use WildWolf\FBR\Response\RecognitionStats;
use WildWolf\FBR\Response\MatchedFaces;
use WildWolf\FBR\Response\StartCompareAck;
use WildWolf\FBR\Response\UploadCompareAck;
use WildWolf\FBR\Response\CompareCompleted;
use WildWolf\FBR\Response\DBStats;
use WildWolf\FBR\Response\Service\QueryFacesAck;
use WildWolf\FBR\Response\Service\QueryFacesStatus;
use WildWolf\FBR\Response\Service\QueryFacesResult;
use WildWolf\FBR\Response\Service\QueryFaceStatsAck;
use WildWolf\FBR\Response\Service\QueryFaceStatsResult;
use WildWolf\FBR\Response\Service\PrepareAddAck;
use WildWolf\FBR\Response\Service\PrepareAddStatus;
use WildWolf\FBR\Response\Service\CapturedFaces as PrepareCapturedFaces;
use WildWolf\FBR\Response\Service\AddFacesAck;
use WildWolf\FBR\Response\Service\DeleteAck;
use WildWolf\FBR\Response\Service\DeleteStatus;

class ResponseFactory
{
    public static function create(\stdClass $data)
    {
        switch ($data->ans_type) {
            case Base::TYPE_GET_DBSTATS:        return new DBStats($data);

            case Base::TYPE_START_CMP_ACK:      return new StartCompareAck($data);
            case Base::TYPE_UPLOAD_CMP_ACK:     return new UploadCompareAck($data);
            case Base::TYPE_CMP_COMPLETED:      return new CompareCompleted($data);

            case Base::TYPE_UPLOAD_SRCH_ACK:    return new SearchUploadAck($data);
            case Base::TYPE_UPLOAD_SRCH_ERR:    return new SearchUploadError($data);
            case Base::TYPE_SRCH_IN_PROGRESS:   return new SearchInProgress($data);
            case Base::TYPE_SRCH_FAILED:        return new SearchFailed($data);
            case Base::TYPE_SRCH_COMPLETED:     return new SearchCompleted($data);

            case Base::TYPE_CAPTURED_FACES:     return new CapturedFaces($data);
            case Base::TYPE_RECOGNITION_STATS:  return new RecognitionStats($data);
            case Base::TYPE_GET_MATCHED_FACES:  return new MatchedFaces($data);

            case Base::TYPE_QUERY_FACES:        return new QueryFacesAck($data);
            case Base::TYPE_QF_STATUS:          return new QueryFacesStatus($data);
            case Base::TYPE_QF_RESULT:          return new QueryFacesResult($data);
            case Base::TYPE_GET_FSTATS:         return new QueryFaceStatsAck($data);
            case Base::TYPE_FSTATS_RESULT:      return new QueryFaceStatsResult($data);

            case Base::TYPE_PREPARE_ADD:        return new PrepareAddAck($data);
            case Base::TYPE_PREPARE_ADD_STATUS: return new PrepareAddStatus($data);
            case Base::TYPE_PREPARE_GET_FACES:  return new PrepareCapturedFaces($data);
            case Base::TYPE_ADD_FACES:          return new AddFacesAck($data);

            case Base::TYPE_DELETE_ACK:         return new DeleteAck($data);
            case Base::TYPE_DELETE_STATUS:      return new DeleteStatus($data);

            default:                            return new Base($data);
        }
    }
}

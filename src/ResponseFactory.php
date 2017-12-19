<?php

namespace WildWolf\FBR;

use WildWolf\FBR\Response\Base;
use WildWolf\FBR\Response\CapturedFaces;
use WildWolf\FBR\Response\CompareCompleted;
use WildWolf\FBR\Response\MatchedFaces;
use WildWolf\FBR\Response\RecognitionStats;
use WildWolf\FBR\Response\SearchCompleted;
use WildWolf\FBR\Response\SearchFailed;
use WildWolf\FBR\Response\SearchInProgress;
use WildWolf\FBR\Response\SearchUploadAck;
use WildWolf\FBR\Response\SearchUploadError;
use WildWolf\FBR\Response\StartCompareAck;
use WildWolf\FBR\Response\UploadCompareAck;
use WildWolf\FBR\Response\Service\AddFacesAck;
use WildWolf\FBR\Response\Service\DeleteAck;
use WildWolf\FBR\Response\Service\DeleteStatus;
use WildWolf\FBR\Response\Service\PrepareAddAck;
use WildWolf\FBR\Response\Service\PrepareAddStatus;
use WildWolf\FBR\Response\Service\PreparedFaces;
use WildWolf\FBR\Response\Service\QuerySectorAck;
use WildWolf\FBR\Response\Service\QuerySectorResult;
use WildWolf\FBR\Response\Service\QuerySectorStatus;
use WildWolf\FBR\Response\Service\QuerySegmentAck;
use WildWolf\FBR\Response\Service\QuerySegmentResult;
use WildWolf\FBR\Response\Service\QuerySegmentsResult;

class ResponseFactory
{
    public static function create(\stdClass $data)
    {
        switch ($data->ans_type) {
            case Base::TYPE_GET_DBSTATS:           return new QuerySegmentsResult($data);

            case Base::TYPE_START_CMP_ACK:         return new StartCompareAck($data);
            case Base::TYPE_UPLOAD_CMP_ACK:        return new UploadCompareAck($data);
            case Base::TYPE_CMP_COMPLETED:         return new CompareCompleted($data);

            case Base::TYPE_UPLOAD_SRCH_ACK:       return new SearchUploadAck($data);
            case Base::TYPE_UPLOAD_SRCH_ERR:       return new SearchUploadError($data);
            case Base::TYPE_SRCH_IN_PROGRESS:      return new SearchInProgress($data);
            case Base::TYPE_SRCH_FAILED:           return new SearchFailed($data);
            case Base::TYPE_SRCH_COMPLETED:        return new SearchCompleted($data);

            case Base::TYPE_CAPTURED_FACES:        return new CapturedFaces($data);
            case Base::TYPE_RECOGNITION_STATS:     return new RecognitionStats($data);
            case Base::TYPE_GET_MATCHED_FACES:     return new MatchedFaces($data);

            case Base::TYPE_SECTOR_REQUEST_ACK:    return new QuerySectorAck($data);
            case Base::TYPE_SECTOR_REQUEST_STATUS: return new QuerySectorStatus($data);
            case Base::TYPE_SECTOR_REQUEST_RESULT: return new QuerySectorResult($data);

            case Base::TYPE_SECTOR_STATS_ACK:      return new QuerySegmentAck($data);
            case Base::TYPE_SECTOR_STATS_RESULT:   return new QuerySegmentResult($data);

            case Base::TYPE_INSERT_ACK:            return new PrepareAddAck($data);
            case Base::TYPE_INSERT_STATUS:         return new PrepareAddStatus($data);
            case Base::TYPE_INSERT_GET_FACES:      return new PreparedFaces($data);
            case Base::TYPE_INSERT_PROCESS:        return new AddFacesAck($data);

            case Base::TYPE_DELETE_ACK:            return new DeleteAck($data);
            case Base::TYPE_DELETE_STATUS:         return new DeleteStatus($data);

            default:                               return new Base($data);
        }
    }
}

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

class ResponseFactory
{
    public static function create(\stdClass $data)
    {
        switch ($data->ans_type) {
            case Base::TYPE_UPLOAD_ACK:  return new UploadAck($data);
            case Base::TYPE_UPLOAD_ERR:  return new UploadError($data);
            case Base::TYPE_IN_PROGRESS: return new InProgress($data);
            case Base::TYPE_FAILED:      return new Failed($data);
            case Base::TYPE_COMPLETED:   return new ResultReady($data);
            case Base::TYPE_GET_RSTATS:  return new Stats($data);
            case Base::TYPE_GET_MSTATS:  return new MatchStats($data);
            case Base::TYPE_GET_FACES:   return new Match($data);
            default:                     return new Base($data);
        }
    }
}

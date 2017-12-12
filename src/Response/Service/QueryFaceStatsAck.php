<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\Base;

class QueryFaceStatsAck extends Base
{
    public function success() : bool
    {
        return $this->resultCode() == 1;
    }
}

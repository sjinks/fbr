<?php

namespace WildWolf\FBR\Response;

class QueryFaceStatsAck extends Base
{
    public function success() : bool
    {
        return $this->resultCode() == 1;
    }
}

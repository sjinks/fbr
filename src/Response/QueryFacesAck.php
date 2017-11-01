<?php

namespace WildWolf\FBR\Response;

class QueryFacesAck extends Base
{
    public function success() : bool
    {
        return $this->resultCode() == 1;
    }
}

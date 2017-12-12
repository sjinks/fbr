<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\Base;

class AddFacesAck extends Base
{
    public function cacheable() : bool
    {
        return false;
    }

    public function isSuccess() : bool
    {
        return 1 == $this->resultCode();
    }

    public function isFailure() : bool
    {
        return 2 == $this->resultCode();
    }
}
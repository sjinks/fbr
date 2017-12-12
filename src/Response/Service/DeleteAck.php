<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\Base;

class DeleteAck extends Base
{
    public function cacheable() : bool
    {
        return false;
    }
}

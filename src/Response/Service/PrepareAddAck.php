<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\Base;

class PrepareAddAck extends Base
{
    public function cacheable() : bool
    {
        return false;
    }
}

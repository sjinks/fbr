<?php

namespace WildWolf\FBR\Response\Service;

class SvcBaseAck extends SvcBase
{
    public function succeeded() : bool
    {
        return $this->accepted();
    }

    public function cacheable() : bool
    {
        return false;
    }
}

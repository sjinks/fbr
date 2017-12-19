<?php

namespace WildWolf\FBR\Response\Service;

class SvcBaseAck extends SvcBase
{
    public function accepted() : bool
    {
        return $this->resultCode() == 1;
    }

    public function succeeded() : bool
    {
        return $this->accepted();
    }

    public function cacheable() : bool
    {
        return false;
    }
}

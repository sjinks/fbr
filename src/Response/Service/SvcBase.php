<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\Base;

class SvcBase extends Base
{
    public function failed() : bool
    {
        return $this->resultCode() < 0;
    }

    public function accepted() : bool
    {
        return $this->resultCode() == 1;
    }

    public function pending() : bool
    {
        return $this->resultCode() == 2;
    }

    public function succeeded() : bool
    {
        return $this->resultCode() == 3;
    }

    public function cacheable() : bool
    {
        return $this->succeeded() || $this->failed();
    }
}

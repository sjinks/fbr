<?php

namespace WildWolf\FBR\Response;

class InProgress extends Base
{
    public function cacheable() : bool
    {
        return false;
    }
}

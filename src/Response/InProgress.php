<?php

namespace WildWolf\FBR\Response;

class InProgress extends Base
{
    public function cacheable()
    {
        return false;
    }
}

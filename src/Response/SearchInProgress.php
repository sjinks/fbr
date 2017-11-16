<?php

namespace WildWolf\FBR\Response;

class SearchInProgress extends Base
{
    public function cacheable() : bool
    {
        return false;
    }
}

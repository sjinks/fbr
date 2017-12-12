<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\Base;

class PrepareAddStatus extends Base
{
    public function cacheable() : bool
    {
        return $this->resultCode() == 3;
    }

    public function getNumberOfCapturedFaces() : int
    {
        return (int)$this->resultsAmount();
    }
}

<?php

namespace WildWolf\FBR\Response\Service;

/**
 * Response Type: 205
 */
class PrepareAddStatus extends SvcBase
{
    public function pending() : bool
    {
        $rc = $this->resultCode();
        return $rc == 2 || $rc == 4;
    }

    public function getNumberOfCapturedFaces() : int
    {
        return (int)$this->resultsAmount();
    }
}

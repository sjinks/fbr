<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\ArrayTraits;
use WildWolf\FBR\Response\Base;

class CapturedFaces extends Base implements \Countable, \Iterator, \ArrayAccess
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $faces = [];
        if ($this->isSuccess()) {
            foreach ($this->fotos as $x) {
                $idx  = (int)$x->par1;
                $face = (string)$x->foto;

                $faces[$idx] = new Parts\CapturedFace($face);
            }

            ksort($faces);
        }

        $this->fotos = $faces;
    }

    public function getNumberOfCapturedFaces() : int
    {
        return (int)$this->resultsAmount();
    }

    public function cacheable() : bool
    {
        return $this->isSuccess() || $this->isFailure();
    }

    public function isSuccess() : bool
    {
        return 3 == $this->resultCode();
    }

    public function isFailure() : bool
    {
        return 2 == $this->resultCode();
    }
}

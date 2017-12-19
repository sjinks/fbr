<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\ArrayTraits;

/**
 * Response Type: 206
 */
class PreparedFaces extends SvcBase implements \Countable, \Iterator, \ArrayAccess
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $faces = [];
        if ($this->succeeded()) {
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
}

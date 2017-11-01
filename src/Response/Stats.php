<?php

namespace WildWolf\FBR\Response;

class Stats extends Base implements \Countable, \Iterator, \ArrayAccess
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $faces = [];
        foreach ($this->fotos as $x) {
            $idx  = (int)$x->par1;
            $min  = (int)$x->par2;
            $max  = (int)$x->par3;
            $face = (string)$x->foto;

            $faces[$idx-1] = new Parts\RecognizedFace($min, $max, $face);
        }

        ksort($faces);
        $this->fotos = $faces;
    }
}

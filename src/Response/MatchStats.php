<?php

namespace WildWolf\FBR\Response;

class MatchStats extends Base implements \Countable, \Iterator, \ArrayAccess
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $faces = [];
        foreach ($this->fotos as $x) {
            $cnt     = (int)$x->par1;
            $min     = (int)$x->par2;
            $max     = (int)$x->par3;

            $faces[] = new Parts\MatchStats($cnt, $min, $max);
        }

        $this->fotos = $faces;
    }
}

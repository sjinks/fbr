<?php

namespace WildWolf\FBR\Response;

class MatchedFaces extends Base implements \Countable, \Iterator, \ArrayAccess
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $matches = [];
        foreach ($this->fotos as $x) {
            $number        = (int)$x->par1;
            $idx           = (int)$x->par2;
            $similarity    = (int)$x->par3;
            $path          = (string)$x->path;
            $face          = (string)$x->foto;

            $matches[$idx] = new Parts\Match($number, $similarity, $face, $path);
        }

        ksort($matches);
        $this->fotos = $matches;
    }
}

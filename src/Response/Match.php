<?php

namespace WildWolf\FBR\Response;

use WildWolf\FBR\Match as FaceMatch;

class Match extends Base implements \Countable, \Iterator, \ArrayAccess
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

            $matches[$idx] = new FaceMatch($number, $similarity, $face, $path);
        }

        $this->fotos = $matches;
    }
}

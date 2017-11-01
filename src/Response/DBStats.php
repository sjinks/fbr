<?php

namespace WildWolf\FBR\Response;

class DBStats extends Base implements \Countable, \Iterator, \ArrayAccess
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $d = [];
        foreach ($this->fotos as $x) {
            $segment  = (int)$x->par1;
            $size     = (int)$x->par2;
            $records  = (int)$x->par3;
            $bank     = (string)$x->path;
            $d[]      = new Parts\SegmentStats($segment, $size, $records, $bank);
        }

        $this->fotos = $d;
    }

    public function cacheable() : bool
    {
        return false;
    }
}

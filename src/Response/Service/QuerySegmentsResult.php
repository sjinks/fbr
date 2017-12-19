<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\ArrayTraits;
use WildWolf\FBR\Response\Base;

/**
 * Response Type: 8
 */
class QuerySegmentsResult extends Base implements \Countable, \Iterator, \ArrayAccess
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

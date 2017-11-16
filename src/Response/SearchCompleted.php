<?php

namespace WildWolf\FBR\Response;

class SearchCompleted extends Base implements \Countable, \Iterator, \ArrayAccess
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $counts = [];
        foreach ($this->fotos as $x) {
            $idx = (int)$x->par1 - 1;
            $cnt = (int)$x->par2;

            $counts[$idx] = $cnt;
        }

        ksort($counts);
        $this->fotos = $counts;
    }
}

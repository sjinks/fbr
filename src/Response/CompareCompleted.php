<?php

namespace WildWolf\FBR\Response;

class CompareCompleted extends Base implements \Countable, \Iterator, \ArrayAccess
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $matches = [];
        foreach ($this->fotos as $x) {
            $sim       = (int)$x->par2;
            $name      = $x->namef;
            $matches[] = new Parts\CompareResult($sim, $name);
        }

        $this->fotos = $matches;
    }

    public function pending() : bool
    {
        return $this->result_code == 2;
    }

    public function cacheable() : bool
    {
        return ($this->result_code == 3) || ($this->result_code == -7);
    }
}

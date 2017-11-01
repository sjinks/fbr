<?php

namespace WildWolf\FBR\Response\Parts;

class CompareResult
{
    private $similarity;
    private $name;

    public function __construct(int $sim, $name)
    {
        $this->similarity = $sim;
        $this->name       = $name;
    }

    public function similarity() : int
    {
        return $this->similarity;
    }

    public function name()
    {
        return $this->name;
    }
}

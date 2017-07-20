<?php

namespace WildWolf\FBR;

class MatchStats
{
    private $results;
    private $min_confidence;
    private $max_confidence;

    public function __construct(int $cnt, int $min, int $max)
    {
        $this->results        = $cnt;
        $this->min_confidence = $min;
        $this->max_confidence = $max;
    }

    public function numberOfResults() : int
    {
        return $this->results;
    }

    public function minConfidence() : int
    {
        return $this->min_confidence;
    }

    public function maxConfidence() : int
    {
        return $this->max_confidence;
    }
}

<?php

namespace WildWolf\FBR\Response\Parts;

class SegmentStats
{
    private $segment;
    private $size;
    private $records;
    private $bank_stats;

    public function __construct(int $segment, int $size, int $records, string $bank_stats)
    {
        $this->segment    = $segment;
        $this->size       = $size;
        $this->records    = $records;
        $this->bank_stats = explode(';', $bank_stats);
    }

    public function segment() : int
    {
        return $this->segment;
    }

    public function size() : int
    {
        return $this->size;
    }

    public function records() : int
    {
        return $this->records;
    }

    public function bankStats() : array
    {
        return $this->bank_stats;
    }
}

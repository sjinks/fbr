<?php

namespace WildWolf\FBR;

class Match
{
    use FaceTraits;

    private $number;
    private $similarity;
    private $path;

    public function __construct(int $nr, int $similarity, string $face, string $path)
    {
        $this->number     = $nr;
        $this->similarity = $similarity;
        $this->face       = $face;
        $this->path       = $path;
    }

    public function faceNumber() : int
    {
        return $this->number;
    }

    public function similarity() : int
    {
        return $this->similarity;
    }

    public function path() : string
    {
        return $this->path;
    }
}

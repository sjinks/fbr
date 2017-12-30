<?php

namespace WildWolf\FBR\Response\Parts;

use WildWolf\FBR\FaceTraits;

class Match
{
    use FaceTraits;

    private $number;
    private $similarity;
    private $path;
    private $namef;
    private $namel;

    public function __construct(int $nr, int $similarity, string $face, string $path, string $namef, string $namel)
    {
        $this->number     = $nr;
        $this->similarity = $similarity;
        $this->face       = $face;
        $this->path       = $path;
        $this->namef      = $namef;
        $this->namel      = $namel;
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

    public function namef() : string
    {
        return $this->namef;
    }

    public function namel() : string
    {
        return $this->namel;
    }
}

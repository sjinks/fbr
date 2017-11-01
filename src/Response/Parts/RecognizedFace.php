<?php

namespace WildWolf\FBR\Response\Parts;

use WildWolf\FBR\FaceTraits;

class RecognizedFace
{
    use FaceTraits;

    private $min_confidence;
    private $max_confidence;

    public function __construct(int $min, int $max, string $face)
    {
        $this->min_confidence = $min;
        $this->max_confidence = $max;
        $this->face           = $face;
    }

    public function minConfidence() : int
    {
        return $this->min_confidence;
    }

    public function maxConfidence() : int
    {
        return $this->max_confidence;
    }

    public function found() : bool
    {
        return $this->max_confidence > 0;
    }
}

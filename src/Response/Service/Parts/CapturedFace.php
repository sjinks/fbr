<?php

namespace WildWolf\FBR\Response\Service\Parts;

use WildWolf\FBR\FaceTraits;

class CapturedFace
{
    use FaceTraits;

    public function __construct(string $face)
    {
        $this->face = $face;
    }
}

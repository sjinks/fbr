<?php

namespace WildWolf\FBR;

trait FaceTraits
{
    public function face() : string
    {
        return $this->face;
    }

    public function faceBinary() : string
    {
        return base64_decode($this->face);
    }

    public function faceAsDataUri() : string
    {
        return 'data:image/jpeg;base64,' . $this->face;
    }
}

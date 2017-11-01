<?php

namespace WildWolf\FBR\Response\Parts;

use WildWolf\FBR\FaceTraits;

class SavedPhoto
{
    use FaceTraits;

    private $bank;
    private $id;
    private $intname;
    private $name;
    private $path;
    private $dt = null;

    public function __construct(int $bank, int $id, string $face, string $intname, string $name, string $path)
    {
        $this->bank    = $bank;
        $this->id      = $id;
        $this->intname = $intname;
        $this->name    = substr($name, 1, -2);
        $this->face    = $face;

        $matches = [];
        if (preg_match('/^(.*?) ([0-9]{1,2}\.[0-9]{2}\.[0-9]{4} [0-9]{1,2}:[0-9]{2}:[0-9]{2})$/', $path, $matches)) {
            $this->path = $matches[1];
            $this->dt   = new \DateTime($matches[2]);
        }
        else {
            $this->path = $path;
        }
    }

    public function bank() : int
    {
        return $this->bank;
    }

    public function id() : int
    {
        return $this->id;
    }

    public function intname() : string
    {
        return $this->intname;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function path() : string
    {
        return $this->path;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function dt()
    {
        return $this->dt;
    }
}

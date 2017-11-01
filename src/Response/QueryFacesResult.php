<?php

namespace WildWolf\FBR\Response;

class QueryFacesResult extends Base
{
    use ArrayTraits;

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        $data = [];
        foreach ($this->fotos as $x) {
            $idx       = (int)$x->par1 - 1;
            $bank      = (int)$x->par2;
            $id        = (int)$x->par3;
            $face      = (string)$x->foto;
            $intname   = (string)$x->namef;
            $name      = (string)$x->namel;
            $path      = (string)$x->path;

            $matches[$idx-1] = new Parts\SavedPhoto($bank, $id, $face, $intname, $name, $path);
        }

        $this->fotos = $data;
    }
}

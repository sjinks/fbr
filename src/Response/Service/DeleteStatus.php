<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\Base;

class DeleteStatus extends Base
{
    private $list = [];

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        if ($this->resultCode() == 3 && !empty($this->fotos[0]->foto)) {
            $this->decodeList($this->fotos[0]->foto);
        }
    }

    private function decodeList($encoded)
    {
        $this->list = explode("\n", rtrim(base64_decode($encoded), "\r\n"));
    }

    public function pending() : bool
    {
        return $this->resultCode() == 2;
    }

    public function cacheable() : bool
    {
        return !$this->pending();
    }

    public function list() : array
    {
        return $this->list;
    }
}

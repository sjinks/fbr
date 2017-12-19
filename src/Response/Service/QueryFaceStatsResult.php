<?php

namespace WildWolf\FBR\Response\Service;

use WildWolf\FBR\Response\Base;

class QueryFaceStatsResult extends Base
{
    private $list = [];

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);
        if ($this->isSuccess() && !empty($this->fotos[0]->foto)) {
            $this->decodeList($this->fotos[0]->foto);
        }
    }

    private function decodeList($encoded)
    {
        $decoded = preg_split('/[\\r\\n]+/', base64_decode($encoded), -1, PREG_SPLIT_NO_EMPTY);
        $list    = [];
        foreach ($decoded as $x) {
            list($sector, $count) = explode('*', $x);
            $list[strtolower($sector)] = $count;
        }

        $this->list = $list;
    }

    public function pending() : bool
    {
        return $this->resultCode() == 2;
    }

    public function isSuccess()
    {
        return $this->resultCode() == 3;
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

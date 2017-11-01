<?php

namespace WildWolf\FBR\Response;

class QueryFaceStatsResult extends Base
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
        $decoded = array_map('trim', explode("\n", trim(base64_decode($encoded))));
        $list    = [];
        foreach ($decoded as $x) {
            list($id, $count) = explode('*', $x);
            $list[$id] = $count;
        }

        $this->list = $list;
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

<?php

namespace WildWolf\FBR\Response;

class QueryFacesStatus extends Base
{
    private $list = [];

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        if ($this->resultCode() == 3) {
            $this->decodeList($this->fotos);
        }
    }

    private function decodeList($encoded)
    {
        $decoded = base64_decode($encoded);
        $list    = [];
        foreach ($decoded as $x) {
            list($segment, $bank, $id, $intname, $name) = explode('*', $x);
            $list["{$segment}*{$bank}*{$id}"] = [$intname, substr($name, 1, -2 - 1)];
        }

        $this->list = $list;
    }

    public function cacheable() : bool
    {
        return $this->resultCode() != 2;
    }

    public function list() : array
    {
        return $this->list;
    }
}
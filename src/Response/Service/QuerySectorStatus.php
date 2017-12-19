<?php

namespace WildWolf\FBR\Response\Service;

/**
 * Response Type: 193
 */
class QuerySectorStatus extends SvcBase
{
    private $list = [];

    public function __construct(\stdClass $data)
    {
        parent::__construct($data);

        if ($this->succeeded() && !empty($this->fotos[0]->foto)) {
            $this->decodeList($this->fotos[0]->foto);
        }
    }

    private function decodeList($encoded)
    {
        $decoded = preg_split('/[\\r\\n]+/', base64_decode($encoded), -1, PREG_SPLIT_NO_EMPTY);
        $list    = [];
        foreach ($decoded as $x) {
            list($segment, $bank, $id, $intname, $name) = explode('*', $x);
            $list[$segment . '*' . $bank . '*' . $id] = [$intname, substr($name, 1, -2)];
        }

        $this->list = $list;
    }

    public function list() : array
    {
        return $this->list;
    }
}

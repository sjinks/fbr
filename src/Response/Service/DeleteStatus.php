<?php

namespace WildWolf\FBR\Response\Service;

/**
 * Response Type: 209
 */
class DeleteStatus extends SvcBase
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
        $this->list = preg_split('/[\\r\\n]+/', base64_decode($encoded), -1, PREG_SPLIT_NO_EMPTY);
    }

    public function list() : array
    {
        return $this->list;
    }
}

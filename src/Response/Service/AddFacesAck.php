<?php

namespace WildWolf\FBR\Response\Service;

/**
 * Response Type: 207
 */
class AddFacesAck extends SvcBaseAck
{
    public function accepted() : bool
    {
        return 4 == $this->result_code;
    }
}

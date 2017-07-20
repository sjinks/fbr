<?php

namespace WildWolf\FBR\Response;

class Base
{
    const TYPE_UPLOAD_ACK  =  33;
    const TYPE_UPLOAD_ERR  =  34;
    const TYPE_IN_PROGRESS =  65;
    const TYPE_FAILED      =  66;
    const TYPE_COMPLETED   =  67;
    const TYPE_GET_RSTATS  =  80;
    const TYPE_GET_MSTATS  = 128;
    const TYPE_GET_FACES   = 129;

    protected $ans_type;
    protected $client_id;
    protected $reqID_serv;
    protected $reqID_clnt;
    protected $segment;
    protected $datetime;
    protected $result_code;
    protected $results_amount;
    protected $comment;
    protected $fotos;

    public function __construct(\stdClass $data)
    {
        $this->ans_type       = $data->ans_type;
        $this->client_id      = $data->data->client_id;
        $this->reqID_clnt     = $data->data->reqID_clnt;
        $this->reqID_serv     = $data->data->reqID_serv;
        $this->segment        = $data->data->segment;
        $this->datetime       = $data->data->datetime;
        $this->result_code    = $data->data->result_code;
        $this->results_amount = $data->data->results_amount;
        $this->comment        = $data->data->comment;
        $this->fotos          = $data->data->fotos;
    }

    public function type() : int
    {
        return $this->ans_type;
    }

    public function clientId()
    {
        return $this->client_id;
    }

    public function serverRequestId() : string
    {
        return $this->reqID_serv;
    }

    public function clientReqiestId() : string
    {
        return $this->reqID_clnt;
    }

    public function segment()
    {
        return $this->segment;
    }

    public function dateTime() : \DateTimeInterface
    {
        return new \DateTime($this->datetime);
    }

    public function resultCode()
    {
        return $this->result_code;
    }

    public function comment()
    {
        return $this->comment;
    }
}

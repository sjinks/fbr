<?php

namespace WildWolf\FBR\Response;

class Base
{
    const TYPE_BASE_STATUS           =   8;

    const TYPE_START_CMP_ACK         =  16;
    const TYPE_UPLOAD_CMP_ACK        =  17;
    const TYPE_CMP_COMPLETED         =  18;
    const TYPE_UPLOAD_SRCH_ACK       =  33;
    const TYPE_UPLOAD_SRCH_ERR       =  34;
    const TYPE_SRCH_IN_PROGRESS      =  65;
    const TYPE_SRCH_FAILED           =  66;
    const TYPE_SRCH_COMPLETED        =  67;
    const TYPE_CAPTURED_FACES        =  80;
    const TYPE_RECOGNITION_STATS     = 128;
    const TYPE_GET_MATCHED_FACES     = 129;

    const TYPE_SECTOR_REQUEST_ACK    = 192;
    const TYPE_SECTOR_REQUEST_STATUS = 193;
    const TYPE_SECTOR_REQUEST_RESULT = 194;
    const TYPE_SECTOR_STATS_ACK      = 200;
    const TYPE_SECTOR_STATS_RESULT   = 201;
    const TYPE_PREPARE_ADD           = 204;
    const TYPE_PREPARE_ADD_STATUS    = 205;
    const TYPE_PREPARE_GET_FACES     = 206;
    const TYPE_ADD_FACES             = 207;
    const TYPE_DELETE_ACK            = 208;
    const TYPE_DELETE_STATUS         = 209;

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

    public function resultsAmount()
    {
        return $this->results_amount;
    }

    public function cacheable() : bool
    {
        return true;
    }
}

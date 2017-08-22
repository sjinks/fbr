<?php

use WildWolf\FBR\Client;
use WildWolf\FBR\Response\InProgress;

class CheckStatusTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var \WildWolf\FBR\Client
     */
    private $_fbr;

    /**
     * @var CurlTestWrapper
     */
    private $_curl;

    private static $_arr65 = [
        "ans_type"  => 65,
        "signature" => "",
        "data"      => [
            "client_id"      => "client",
            "reqID_serv"     => "5388f2d4-ce95-44e3-9b14-7c56a9472725",
            "reqID_clnt"     => "ea0abac1-6f89-4d7f-9e1a-c45429d574ca",
            "segment"        => null,
            "datetime"       => '01.01.2017 00:00:00',
            "result_code"    => 2,
            "results_amount" => 0,
            "comment"        => "processing",
            "fotos"          => []
        ]
    ];

    protected function setUp()
    {
        $this->_fbr  = new Client('http://localhost/', 'client');
        $this->_curl = new CurlTestWrapper();

        $this->_curl->setInfo(CURLINFO_HTTP_CODE, 200);

        $this->_fbr->setCurlWrapper($this->_curl);
    }

    public function testAnswer65()
    {
        $content = json_encode(self::$_arr65);
        $this->_curl->setContent($content);

        $result = $this->_fbr->checkUploadStatus('5388f2d4-ce95-44e3-9b14-7c56a9472725');
        $post   = $this->_curl->getOption(CURLOPT_POSTFIELDS);

        $this->assertInstanceOf(InProgress::class, $result);

        $this->assertTrue($this->_curl->getOption(CURLOPT_POST));

        $req = json_decode($post);
        $this->assertTrue(is_object($req));

        $this->assertEquals(Client::CMD_STATUS, $req->req_type);
        $this->assertNotEmpty($req->data->reqID_serv);
        $this->assertEmpty($req->data->foto);
    }
}

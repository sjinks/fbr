<?php

use WildWolf\FBR\Client;
use WildWolf\FBR\Response\UploadAck;

class UploadTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var \WildWolf\FBR\Client
     */
    private $_fbr;

    /**
     * @var CurlTestWrapper
     */
    private $_curl;

    private static $_arr = [
        "ans_type"  => 33,
        "signature" => "",
        "data"      => [
            "client_id"      => "client",
            "reqID_serv"     => "5388f2d4-ce95-44e3-9b14-7c56a9472725",
            "reqID_clnt"     => "4c97e8e1-4e66-4fe0-b3bf-7695a0e1fa57",
            "segment"        => null,
            "datetime"       => '01.01.2017 00:00:00',
            "result_code"    => 1,
            "results_amount" => 0,
            "comment"        => "OK",
            "fotos"          => []
        ]
    ];

    protected function setUp()
    {
        $this->_fbr  = new Client('http://localhost/', 'client');
        $this->_curl = new CurlTestWrapper();

        $content = json_encode(self::$_arr);
        $this->_curl->setContent($content);
        $this->_curl->setInfo(CURLINFO_HTTP_CODE, 200);

        $this->_fbr->setCurlWrapper($this->_curl);
    }

    public function testStreamUpload()
    {
        $f = fopen(__DIR__ . '/image.jpg', 'r');
        $this->commonChecks($f, true);
    }

    public function testGDUpload()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped("GD is not loaded");
        }

        $im = imagecreatefromjpeg(__DIR__ . '/image.jpg');
        $this->commonChecks($im);
    }

    public function testStringUpload()
    {
        $s = file_get_contents(__DIR__ . '/image.jpg');
        $this->commonChecks($s, true);
    }

    public function testImagickUpload()
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('imagick is not loaded');
        }

        $im = new \Imagick(__DIR__ . '/image.jpg');
        $this->commonChecks($im);
    }

    public function testInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_fbr->uploadFile(null);
    }

    private function commonChecks($r, bool $checkFile = false)
    {
        $result = $this->_fbr->uploadFile($r);
        $post   = $this->_curl->getOption(CURLOPT_POSTFIELDS);

        $this->assertInstanceOf(UploadAck::class, $result);

        $this->assertTrue($this->_curl->getOption(CURLOPT_POST));
        $this->assertRegExp('/^[0-9a-fA-F]+\r\n/', $post);
        $this->assertRegExp('/\r\n0\r\n\r\n$/', $post);

        $m = [];
        $this->assertEquals(1, preg_match('/^[0-9a-fA-F]+\r\n(.*?)\r\n0\r\n\r\n$/s', $post, $m));
        $this->assertTrue(isset($m[1]));

        $req = json_decode($m[1]);
        $this->assertTrue(is_object($req));

        $this->assertEquals(Client::CMD_UPLOAD, $req->req_type);
        $this->assertEmpty($req->data->reqID_serv);
        $this->assertNotEmpty($req->data->foto);

        if ($checkFile) {
            $actual   = base64_decode($req->data->foto);
            $expected = file_get_contents(__DIR__ . '/image.jpg');
            $this->assertEquals($expected, $actual);
        }
    }
}

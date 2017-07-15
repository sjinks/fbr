<?php

class CurlTestWrapper implements \WildWolf\CurlWrapperInterface
{
    private $_options = [];
    private $_info    = [];
    private $_content;

    public function __construct(string $url = null)
    {
        if ($url) {
            $this->_options[CURLOPT_URL] = $url;
        }
    }

    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;
        return true;
    }

    public function setOptions(array $opts)
    {
        foreach ($opts as $k => $v) {
            $this->_options[$k] = $v;
        }

        return true;
    }

    public function getOption(int $option)
    {
        return isset($this->_options[$option]) ? $this->_options[$option] : null;
    }

    public function execute()
    {
        return $this->_content;
    }

    public function setContent($content)
    {
        $this->_content = $content;
    }

    public function reset()
    {
        return true;
    }

    public function doReset()
    {
        $this->_options = [];
        $this->_info    = [];
        $this->_content = '';
    }

    public function info(int $key = null)
    {
        return null === $key
            ? $this->_info
            : (isset($this->_info[$key]) ? $this->_info[$key] : null)
        ;
    }

    public function setInfo(int $key, $val)
    {
        $this->_info[$key] = $val;
    }

    public function error()
    {
        return '';
    }

    public function errno()
    {
        return 0;
    }
}

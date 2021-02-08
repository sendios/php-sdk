<?php

namespace Sendios\Services;

class CurlRequest
{
    private $handle = null;
    protected $permanentOptions = [];

    public function __construct($url = '')
    {
        $this->handle = curl_init($url);
    }

    public function setOption($name, $value, $permanentOption = false)
    {
        if ($permanentOption) {
            $this->permanentOptions[$name] = $value;
        } else {
            curl_setopt($this->handle, $name, $value);
        }
    }

    public function execute()
    {
        $this->setPermanentOptions();
        return curl_exec($this->handle);
    }

    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    public function close()
    {
        curl_close($this->handle);
    }

    public function reset()
    {
        curl_reset($this->handle);
    }

    public function resetPermanentOptions()
    {
        $this->permanentOptions = [];
    }

    public function getErrorNo()
    {
        return curl_errno($this->handle);
    }

    public function getError()
    {
        return curl_error($this->handle);
    }


    protected function setPermanentOptions()
    {
        foreach ($this->permanentOptions as $name => $value) {
            $this->setOption($name, $value);
        }
    }

}

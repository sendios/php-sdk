<?php

namespace Sendios\Services;

class CurlRequest
{
    private $handle;
    protected $permanentOptions = [];

    public function __construct(string $url = '')
    {
        $this->handle = curl_init($url);
    }

    public function setOption($name, $value, $permanentOption = false): void
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

    public function close(): void
    {
        curl_close($this->handle);
    }

    public function reset(): void
    {
        curl_reset($this->handle);
    }

    public function resetPermanentOptions(): void
    {
        $this->permanentOptions = [];
    }

    public function getErrorNo(): int
    {
        return curl_errno($this->handle);
    }

    public function getError(): string
    {
        return curl_error($this->handle);
    }


    protected function setPermanentOptions(): void
    {
        foreach ($this->permanentOptions as $name => $value) {
            $this->setOption($name, $value);
        }
    }
}

<?php

namespace Sendios\Http;

class Response
{
    private $rawResult;
    private $code;
    private $data = [];
    private $meta = [];

    public function __construct($curlResponse)
    {
        $this->rawResult = $curlResponse;
        if (isset($curlResponse['code'])) {
            $this->code = $curlResponse['code'];
        }
        if (!empty($curlResponse['result'])) {
            $decodedResult = json_decode($curlResponse['result'], true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                return;
            }
            if (!empty($decodedResult['_meta'])) {
                $this->meta = $decodedResult['_meta'];
            }
            if (!empty($decodedResult['data'])) {
                $this->data = $decodedResult['data'];
            }
        }
    }

    public function getRawResult()
    {
        return $this->rawResult;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMeta()
    {
        return $this->meta;
    }
}

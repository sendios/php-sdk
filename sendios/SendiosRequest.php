<?php

namespace Sendios;

use Sendios\Exception\RequestException;

/**
 * Class SendiosRequest
 * @property SendiosCurlRequest $curlRequest
 * @property string $apiBase
 * @property string $api3Base
 */
class SendiosRequest extends SendiosDi
{
    const API_BASE = 'https://api.sendios.io/v1/';
    const API3_BASE = 'https://api.sendios.io/v3/';

    private $curlRequest = null;
    private $lastCurlResult = null;
    private $apiBase;
    private $api3Base;

    /**
     * SendiosRequest constructor.
     * @param $di
     */
    public function __construct($di)
    {
        parent::__construct($di);
        $this->setApiBase();
        $this->setApi3Base();
        $this->setCurlRequest(new SendiosCurlRequest());
    }

    /**
     * @param SendiosCurlRequest $curlRequest
     */
    public function setCurlRequest(SendiosCurlRequest $curlRequest)
    {
        $this->curlRequest = $curlRequest;
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool
     */
    public function receive($resource, array $data = [])
    {
        return $this->send($resource, 'GET', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool
     */
    public function create($resource, array $data = [])
    {
        return $this->send($resource, 'POST', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool
     */
    public function update($resource, array $data)
    {
        return $this->send($resource, 'PUT', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool
     */
    public function delete($resource, $data = [])
    {
        return $this->send($resource, 'DELETE', $data);
    }

    /**
     * @return SendiosResponse last request result
     */
    public function getLastResponse()
    {
        return new SendiosResponse($this->lastCurlResult);
    }

    /**
     * Closed session
     */
    public function close()
    {
        $this->curlRequest->close();
    }

    /**
     * @param string $resource
     * @param string $method
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    private function send($resource, $method, $data = [])
    {
        $method = strtoupper($method);
        $uri = $this->apiBase . $resource;

        $headers = [];

        $headers[] = 'Authorization: Basic ' . base64_encode($this->clientId . ':' . sha1($this->clientKey));
        $result = $this->sendCurl($uri, $method, $data, $headers);
        $this->lastCurlResult = $result;
        if ($result['code'] != 200) {
            $debugData = [
                'uri' => $uri,
                'method' => $method,
                'data' => $data,
                'headers' => $headers,
            ];

            $exceptionMsg = $this->buildExceptionMessage($this->curlRequest, $result, $debugData);
            $this->errorHandler->handle(new RequestException($exceptionMsg, $result['code']));

            return false;
        }
        $result = json_decode($result['result'], true);
        if (!$result) {
            return false;
        }
        if (isset($result['data'])) {
            return $result['data'];
        }

        return true;
    }

    /**
     * @param $uri
     * @param $method
     * @param $data
     * @param $headers
     * @return array
     */
    private function sendCurl($uri, $method, $data, $headers)
    {
        $this->curlRequest->setOption(CURLOPT_URL, $uri);
        if (count($data)) {
            $this->curlRequest->setOption(CURLOPT_POSTFIELDS, json_encode($data));
        }
        $this->curlRequest->setOption(CURLOPT_HTTPHEADER, $headers);
        $this->curlRequest->setOption(CURLOPT_RETURNTRANSFER, 1);
        $this->curlRequest->setOption(CURLOPT_CUSTOMREQUEST, $method);

        $result = $this->curlRequest->execute();
        $code = $this->curlRequest->getInfo(CURLINFO_HTTP_CODE);

        $this->curlRequest->reset();

        return [
            'result' => $result,
            'code' => $code
        ];
    }

    public function sendToApi3($resource, $method, $data = [])
    {
        $uri = $this->api3Base . $resource;

        $headers = $this->getApi2Headers();

        $result = $this->sendCurl($uri, $method, $data, $headers);
        $this->lastCurlResult = $result;
        if (substr($result['code'], 0, 1) != 2) { //2xx
            $debugData = [
                'uri' => $uri,
                'method' => $method,
                'data' => $data,
                'headers' => $headers
            ];

            $exceptionMsg = $this->buildExceptionMessage($this->curlRequest, $result, $debugData);
            $this->errorHandler->handle(new RequestException($exceptionMsg, $result['code']));

            return false;
        }
        $result = json_decode($result['result'], true);
        if (!$result) {
            return false;
        }
        if (isset($result['data'])) {
            return $result['data'];
        }

        return true;
    }

    public function setOption($name, $value, $permanentOption = false)
    {
        $this->curlRequest->setOption($name, $value, $permanentOption);
    }

    public function resetOptions()
    {
        $this->curlRequest->reset();
    }

    public function resetPermanentOptions()
    {
        $this->curlRequest->resetPermanentOptions();
    }

    private function setApiBase()
    {
        $this->apiBase = str_replace('client_id', $this->clientId, self::API_BASE);
    }

    private function setApi3Base()
    {
        $this->api3Base = str_replace('client_id', $this->clientId, self::API3_BASE);
    }

    /**
     * @return array
     */
    private function getApi2Headers()
    {
        return [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . sha1($this->clientKey))
        ];
    }

    /**
     * @param SendiosCurlRequest $curlRequest
     * @param array $result
     * @param array $debugData
     * @return string
     */
    private function buildExceptionMessage(SendiosCurlRequest $curlRequest, array $result, array $debugData)
    {
        $exceptionMsg = 'Request failed: code ' . $result['code'] . ' ' . $result['result'] . ' Request data: ' . json_encode($debugData);
        if ($curlRequest->getErrorNo()) {
            $exceptionMsg .= ' CURL_ERROR: ' . $curlRequest->getError();
        }

        return $exceptionMsg;
    }
}

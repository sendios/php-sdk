<?php

namespace Sendios\Http;

use Sendios\Exception\RequestException;
use Sendios\Services\CurlRequest;
use Sendios\Services\ErrorHandler;

/**
 * Class SendiosRequest
 * @property CurlRequest $curlRequest
 * @property string $apiBase
 * @property string $api3Base
 */
class Request
{
    private const API_BASE = 'https://api.sendios.io/v1/';
    private const API3_BASE = 'https://api.sendios.io/v3/';

    private $clientId;
    private $clientKey;
    private $errorHandler;
    private $curlRequest;
    private $lastCurlResult;
    private $apiBase;
    private $api3Base;

    /**
     * Request constructor.
     * @param int $clientId
     * @param string $clientKey
     * @param ErrorHandler $errorHandler
     */
    public function __construct(int $clientId, string $clientKey, ErrorHandler $errorHandler)
    {
        $this->clientId = $clientId;
        $this->clientKey = $clientKey;
        $this->errorHandler = $errorHandler;
        $this->setApiBase();
        $this->setApi3Base();
        $this->setCurlRequest(new CurlRequest());
    }

    /**
     * @param CurlRequest $curlRequest
     */
    public function setCurlRequest(CurlRequest $curlRequest): void
    {
        $this->curlRequest = $curlRequest;
    }

    /**
     * @param string $resource
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function receive(string $resource, array $data = [])
    {
        return $this->send($resource, 'GET', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function create(string $resource, array $data = [])
    {
        return $this->send($resource, 'POST', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function update(string $resource, array $data)
    {
        return $this->send($resource, 'PUT', $data);
    }

    /**
     * @param $resource
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function delete(string $resource, $data = [])
    {
        return $this->send($resource, 'DELETE', $data);
    }

    /**
     * @return Response last request result
     */
    public function getLastResponse(): Response
    {
        return new Response($this->lastCurlResult);
    }

    /**
     * Closed session
     */
    public function close(): void
    {
        $this->curlRequest->close();
    }

    /**
     * @param $resource
     * @param $method
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    private function send(string $resource, string $method, array $data = [])
    {
        $method = strtoupper($method);
        $uri = $this->apiBase . $resource;

        $headers = [];

        $headers[] = 'Authorization: Basic ' . base64_encode($this->clientId . ':' . sha1($this->clientKey));
        $result = $this->sendCurl($uri, $method, $data, $headers);
        $this->lastCurlResult = $result;
        if ($result['code'] !== 200) {
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

        return $result['data'] ?? true;
    }

    /**
     * @param $uri
     * @param $method
     * @param $data
     * @param $headers
     * @return array
     */
    private function sendCurl(string $uri, string $method, array $data, array $headers): array
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

        return $result['data'] ?? true;
    }

    public function setOption($name, $value, $permanentOption = false): void
    {
        $this->curlRequest->setOption($name, $value, $permanentOption);
    }

    public function resetOptions(): void
    {
        $this->curlRequest->reset();
    }

    public function resetPermanentOptions(): void
    {
        $this->curlRequest->resetPermanentOptions();
    }

    private function setApiBase(): void
    {
        $this->apiBase = str_replace('client_id', $this->clientId, self::API_BASE);
    }

    private function setApi3Base(): void
    {
        $this->api3Base = str_replace('client_id', $this->clientId, self::API3_BASE);
    }

    /**
     * @return string[]
     */
    private function getApi2Headers(): array
    {
        return [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . sha1($this->clientKey))
        ];
    }

    /**
     * @param CurlRequest $curlRequest
     * @param array $result
     * @param array $debugData
     * @return string
     */
    private function buildExceptionMessage(CurlRequest $curlRequest, array $result, array $debugData): string
    {
        $exceptionMsg = 'Request failed: code ' . $result['code'] . ' ' . $result['result'] . ' Request data: ' . json_encode($debugData);
        if ($curlRequest->getErrorNo()) {
            $exceptionMsg .= ' CURL_ERROR: ' . $curlRequest->getError();
        }

        return $exceptionMsg;
    }
}

<?php

namespace Tests;

use Sendios\Exception\RequestException;
use Sendios\SendiosSdk;
use PHPUnit\Framework\TestCase;
use Sendios\Services\CurlRequest;
use Sendios\Services\ErrorHandler;

class RequestTest extends TestCase
{
    public function testShouldCheckSuccessReceive()
    {
        $responseData = 'response data';
        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(json_encode(array('data' => $responseData))));
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(200));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $data = $sendios->request->receive('test/get');
        $this->assertEquals($responseData, $data);
    }

    public function testShouldCheck404Receive()
    {
        $this->expectException(RequestException::class);

        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(404));
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $data = $sendios->request->receive('test/get');
        $this->assertFalse($data);
    }

    public function testShouldCheckEmptyDataReceive()
    {
        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(json_encode(array('OK'))));
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(200));
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $data = $sendios->request->receive('test/get');
        $this->assertTrue($data);
    }

    public function testShouldCheckSuccessCreate()
    {
        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(json_encode(array('OK'))));
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(200));
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $data = array(
            'name' => 'John',
            'age' => 23,
            'type' => null,
            'array_data' => array(
                'key' => 'value'
            )
        );
        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $response = $sendios->request->create('test', $data);
        $this->assertTrue($response);
    }

    public function testShouldCheckFailedCreate()
    {
        $this->expectException(RequestException::class);

        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(json_encode(array('Error'))));
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(401));
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $data = array(
            'name' => 'John',
            'age' => 23,
            'type' => null,
            'array_data' => array(
                'key' => 'value'
            )
        );
        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $response = $sendios->request->create('test', $data);
        $this->assertFalse($response);
    }

    public function testShouldCheckSuccessUpdate()
    {
        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(json_encode(array('OK'))));
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(200));
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $data = array(
            'name' => 'John',
            'age' => 23,
            'type' => null,
            'array_data' => array(
                'key' => 'value'
            )
        );
        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $response = $sendios->request->update('test/42', $data);
        $this->assertTrue($response);
    }

    public function testShouldCheckFailedUpdate()
    {
        $this->expectException(RequestException::class);

        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(json_encode(array('Error'))));
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(403));
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $data = array(
            'name' => 'John',
            'age' => 23,
            'type' => null,
            'array_data' => array(
                'key' => 'value'
            )
        );
        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $response = $sendios->request->update('test/42', $data);
        $this->assertFalse($response);
    }

    public function testShouldCheckSuccessDelete()
    {
        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(json_encode(array('OK'))));
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(200));
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';

        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $response = $sendios->request->delete('test/42');
        $this->assertTrue($response);
    }

    public function testShouldCheckFailedDelete()
    {
        $this->expectException(RequestException::class);
        $curl = $this->createMock(CurlRequest::class);
        $curl->expects($this->any())
            ->method('execute')
            ->will($this->returnValue(json_encode(array('Error'))));
        $curl->expects($this->any())
            ->method('getInfo')
            ->will($this->returnValue(500));
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';

        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request->setCurlRequest($curl);
        $response = $sendios->request->delete('test/42');
        $this->assertFalse($response);
    }
}

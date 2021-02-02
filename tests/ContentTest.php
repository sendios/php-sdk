<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Sendios\SendiosSdk;
use Sendios\Services\CurlRequest;

class ContentTest extends TestCase
{
    public function testTrackShow()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sdk = new SendiosSdk($clientId, $clientKey);
        $curlRequest = $this->getMockBuilder(CurlRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $curlRequest->expects($this->once())->method('execute')
            ->will($this->returnValue(json_encode(['result' => true])));
        $curlRequest->expects($this->once())->method('getInfo')
            ->will($this->returnValue(200));

        $sdk->request->setCurlRequest($curlRequest);
        $result = $sdk->content->trackShow(1, 'zxc', 1);
        $this->assertEquals(true, $result);
    }

    public function testTrackShowProjectException()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sdk = new SendiosSdk($clientId, $clientKey);
        $result = $sdk->content->trackShow(false, 'zxc', 1);
        $this->assertEquals(false, $result);
    }

    public function testTrackShowUidException()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sdk = new SendiosSdk($clientId, $clientKey);
        $result = $sdk->content->trackShow(1, false, 1);
        $this->assertEquals(false, $result);
    }

    public function testTrackShowEntityIdException()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sdk = new SendiosSdk($clientId, $clientKey);
        $result = $sdk->content->trackShow(1, 'zxc', false);
        $this->assertEquals(false, $result);
    }
}
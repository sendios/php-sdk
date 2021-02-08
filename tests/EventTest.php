<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Sendios\Http\Request;
use Sendios\SendiosSdk;

class EventTest extends TestCase
{
    public function testSend()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $predefinedResult = ['status' => true];
        $sdk = new SendiosSdk($clientId, $clientKey);

        $predefinedData = [
          [
              'project_id' => 1,
              'event_id' => 2,
              'receiver_id' => 3,
          ]
        ];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with('product-event/create', $predefinedData)
            ->will($this->returnValue($predefinedResult));

        $sdk->request = $request;

        $result = $sdk->event->send($predefinedData);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testValidationDataIsNotAnArray()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sdk = new SendiosSdk($clientId, $clientKey);

        $result = $sdk->event->send([false]);
        $this->assertEquals(false, $result);
    }

    public function testValidationDataIsWrong()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sdk = new SendiosSdk($clientId, $clientKey);

        $result = $sdk->event->send([[]]);
        $this->assertEquals(false, $result);
    }
}
<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Sendios\Exception\ValidationException;
use Sendios\Http\Request;
use Sendios\SendiosSdk;
use Sendios\Services\ErrorHandler;

class BuyingTest extends TestCase
{
    public function testGetBuyingDecision()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $predefinedResult = ['email' => 'test@mail.buying', 'decision' => true];
        $sdk = new SendiosSdk($clientId, $clientKey);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with('buying/email', [
                'email' => 'test@mail.buying'
            ])
            ->will($this->returnValue($predefinedResult));
        $sdk->request = $request;
        $result = $sdk->buying->getBuyingDecision('test@mail.buying');
        $this->assertEquals($predefinedResult, $result);
    }

    public function testGetBuyingDecisionWrongEmail()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sdk = new SendiosSdk($clientId, $clientKey);
        $result = $sdk->buying->getBuyingDecision(false);
        $this->assertEquals(false, $result);
    }
}

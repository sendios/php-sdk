<?php

namespace Tests;

use Sendios\Sendios;
use Sendios\SendiosEmail;
use Sendios\SendiosErrorHandler;
use Sendios\SendiosRequest;
use PHPUnit\Framework\TestCase;

class SendiosEmailTest extends TestCase
{

    public function testShouldCheckActionCheck()
    {
        $email = 'someone@example.com';
        $predefinedResult = array('data' => 'ok');

        $request = $this->getMockBuilder(SendiosRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with(SendiosEmail::CHECK_EMAIL_RESOURCE, ['email' => $email, 'sanitize' => true])
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->email->check($email);
        $this->assertEquals($predefinedResult, $result);
    }
}

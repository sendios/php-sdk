<?php

namespace Tests;

use Sendios\Http\Request;
use Sendios\Resources\Email;
use Sendios\SendiosSdk;
use PHPUnit\Framework\TestCase;
use Sendios\Services\ErrorHandler;

class EmailTest extends TestCase
{
    public function testShouldCheckActionCheck()
    {
        $email = 'someone@example.com';
        $predefinedResult = array('data' => 'ok');

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with('email/check', ['email' => $email, 'sanitize' => true])
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->email->check($email);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testValidate()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with('email/check/send', ['project' => 1, 'email' => 'test@validate.email'])
            ->will($this->returnValue(['result' => true]));

        $sendios->request = $request;
        $result = $sendios->email->validate(1, 'test@validate.email');
        $this->assertEquals(['result' => true], $result);
    }

    public function testTrackClickByMailId()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with('trackemail/click/777')
            ->will($this->returnValue(['result' => true]));

        $sendios->request = $request;
        $result = $sendios->email->trackClickByMailId(777);
        $this->assertEquals(['result' => true], $result);
    }

    public function testTrackClickByMailIdWrongId()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $result = $sendios->email->trackClickByMailId(false);
        $this->assertEquals(false, $result);
    }
}

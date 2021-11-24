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

    public function testTrackMailClick()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('sendToApi3')
            ->with('client/track/mail/click?u=3&m=2&p=1&t=6&s=5&tp=4')
            ->will($this->returnValue(['result' => true]));

        $sendios->request = $request;

        $projectId = 1;
        $mailId = 2;
        $userId = 3;
        $type = 4;
        $source = 5;
        $typeId = 6;
        $result = $sendios->email->trackMailClick($projectId, $mailId, $userId, $type, $source, $typeId);

        $this->assertEquals(['result' => true], $result);
    }

    public function testTrackMailClickSmallArg()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('sendToApi3')
            ->with('client/track/mail/click?u=3&m=2&p=1')
            ->will($this->returnValue(['result' => true]));

        $sendios->request = $request;

        $projectId = 1;
        $mailId = 2;
        $userId = 3;

        $result = $sendios->email->trackMailClick($projectId, $mailId, $userId);

        $this->assertEquals(['result' => true], $result);
    }

    public function testTrackMailClickFromParams()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('sendToApi3')
            ->with('client/track/mail/click?u=3&m=2&p=1&t=4&s=5&tp=6')
            ->will($this->returnValue(['result' => true]));

        $sendios->request = $request;

        $params = base64_encode(json_encode([
            'p' => 1,
            'm' => 2,
            'u' => 3,
            't' => 4,
            's' => 5,
            'tp' => 6,
        ], JSON_THROW_ON_ERROR));
        $result = $sendios->email->trackMailClickFromParams($params);

        $this->assertEquals(['result' => true], $result);
    }

    public function testTrackMailClickFromParamsSmallArg()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('sendToApi3')
            ->with('client/track/mail/click?u=3&m=2&p=1')
            ->will($this->returnValue(['result' => true]));

        $sendios->request = $request;

        $params = base64_encode(json_encode([
            'p' => 1,
            'm' => 2,
            'u' => 3,
        ], JSON_THROW_ON_ERROR));
        $result = $sendios->email->trackMailClickFromParams($params);

        $this->assertEquals(['result' => true], $result);
    }

    public function testTrackMailClickFromParamsFailString()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $params = 'khvskhrvalhvl';
        $result = $sendios->email->trackMailClickFromParams($params);

        $this->assertFalse($result);
    }

    public function testTrackMailClickFromParamsEmptyJson()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $params = base64_encode(json_encode([], JSON_THROW_ON_ERROR));
        $result = $sendios->email->trackMailClickFromParams($params);

        $this->assertFalse($result);
    }
}

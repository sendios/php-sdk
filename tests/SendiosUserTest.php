<?php

namespace Tests;

use Sendios\Sendios;
use Sendios\SendiosErrorHandler;
use Sendios\SendiosRequest;
use Sendios\SendiosUser;
use PHPUnit\Framework\TestCase;

class SendiosUserTest extends TestCase
{
    public function testShouldCheckGetByEmail()
    {
        $predefinedResult = array('user' => array('id' => 42));
        $projectId = 3;
        $email = 'someone@example.com';

        $request = $this->createMock(SendiosRequest::class);
        $request->expects($this->once())
            ->method('receive')
            ->with('user/project/' . $projectId . '/email/' . $email)
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->user->getByEmail($email, $projectId);
        $this->assertEquals($predefinedResult['user'], $result);
    }

    public function testShouldCheckGetById()
    {
        $predefinedResult = array('user' => array('id' => 42));
        $userId = 42;

        $request = $this->createMock(SendiosRequest::class);
        $request->expects($this->once())
            ->method('receive')
            ->with('user/id/' . $userId)
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->user->getById($userId);
        $this->assertEquals($predefinedResult['user'], $result);
    }

    public function testShouldCheckResolveId()
    {
        $predefinedResult = array('user' => array('id' => 42));
        $userId = 42;

        $request = $this->createMock(SendiosRequest::class);
        $request->expects($this->once())
            ->method('receive')
            ->with('user/id/' . $userId)
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->user->resolve($userId);
        $this->assertEquals($predefinedResult['user'], $result);
    }

    public function testShouldCheckResolveArray()
    {
        $predefinedUser = array(
            'id' => 42,
            'name' => 'John'
        );
        $user = new SendiosUser($this);
        $this->assertEquals($predefinedUser, $user->resolve($predefinedUser));
    }

    public function testShouldCheckPlatforms()
    {
        $user = new SendiosUser($this);
        $this->assertEquals(SendiosUser::PLATFORM_ANDROID, $user->getPlatformAndroid());
        $this->assertEquals(SendiosUser::PLATFORM_IOS, $user->getPlatformIos());
        $this->assertEquals(SendiosUser::PLATFORM_DESKTOP, $user->getPlatformDesktop());
        $this->assertEquals(SendiosUser::PLATFORM_MOBILE, $user->getPlatformMobile());
        $this->assertEquals(SendiosUser::PLATFORM_UNKNOWN, $user->getPlatformUnknown());
    }

    public function testShouldCheckSetUserFieldsByEmailAndProjectId()
    {
        $predefinedResult = true;
        $projectId = 42;
        $email = 'email@example.com';
        $data = ['field1' => 174, 'ctime' => time()];

        $request = $this->createMock(SendiosRequest::class);
        $request->expects($this->once())
            ->method('update')
            ->with('userfields/project/' . $projectId . '/emailhash/' . base64_encode($email), $data)
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->user->setUserFieldsByEmailAndProjectId('email@example.com', $projectId, $data);
        $this->assertTrue($result);
    }
}

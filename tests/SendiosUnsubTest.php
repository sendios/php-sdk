<?php

namespace Tests;

use Sendios\Sendios;
use Sendios\SendiosErrorHandler;
use Sendios\SendiosRequest;
use Sendios\SendiosUnsub;
use PHPUnit\Framework\TestCase;

class SendiosUnsubTest extends TestCase
{
    public function testShouldCheckAddByFbl()
    {
        $predefinedResult = array('data' => 'ok');
        $user = array('id' => 42);

        $request = $this->createMock(SendiosRequest::class);
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/' . $user['id'] . '/source/' . SendiosUnsub::SOURCE_FBL)
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->unsub->addByFbl($user);

        $this->assertEquals($predefinedResult, $result);
    }

    public function testShouldCheckAddByLink()
    {
        $predefinedResult = array('data' => 'ok');
        $user = array('id' => 42);

        $request = $this->createMock(SendiosRequest::class);
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/' . $user['id'] . '/source/' . SendiosUnsub::SOURCE_LINK)
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->unsub->addByLink($user);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testShouldCheckAddByClient()
    {
        $predefinedResult = array('data' => 'ok');
        $user = array('id' => 42);

        $request = $this->getMockBuilder(SendiosRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/' . $user['id'] . '/source/' . SendiosUnsub::SOURCE_CLIENT)
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->unsub->addByClient($user);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testShouldCheckAddBySettings()
    {
        $predefinedResult = array('data' => 'ok');
        $user = array('id' => 42);

        $request = $this->createMock(SendiosRequest::class);
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/' . $user['id'] . '/source/' . SendiosUnsub::SOURCE_SETTINGS)
            ->will($this->returnValue($predefinedResult));

        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $sendios->request = $request;
        $result = $sendios->unsub->addBySettings($user);
        $this->assertEquals($predefinedResult, $result);
    }
}

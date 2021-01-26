<?php

namespace Tests;

use Sendios\Encrypter;
use Sendios\Sendios;
use Sendios\SendiosErrorHandler;
use Sendios\SendiosPush;
use Sendios\SendiosRequest;
use PHPUnit\Framework\TestCase;

class SendiosPushTest extends TestCase
{
    public function testShouldCheckSend()
    {
        $predefinedResult = array('data' => 'ok');
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';

        $data = array( // Data for letter
            'some' => 'hi',
            'letter' => 'John',
            'variables' => '!',
        );
        $meta = array(); // Your additional data

        $sendios = new Sendios($clientId, $clientKey);
        $sendios->errorHandler->setErrorMode(SendiosErrorHandler::MODE_EXCEPTION);
        $params = array(
            'type_id' => 1,
            'category' => $sendios->push->getCategorySystem(),
            'client_id' => $clientId,
            'project_id' => 3,
            'meta' => $meta,
            'user' => array(
                'email' => 'someone@example.com'
            )
        );

        $encrypter = new Encrypter(substr(md5($clientKey), 4, 16));
        $params['value_encrypt']['template_data'] = $encrypter->encrypt($data);

        $request = $this->getMockBuilder(SendiosRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->will($this->returnValue($predefinedResult));

        $sendios->request = $request;
        $result = $sendios->push->send($params['type_id'], $params['category'], $params['project_id'], $params['user']['email'], array('email' => ''), $data, $meta);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testShouldCheckGetCategorySystem()
    {
        $push = new SendiosPush($this);
        $this->assertEquals(SendiosPush::CATEGORY_SYSTEM, $push->getCategorySystem());
    }

    public function testShouldCheckGetCategoryTrigger()
    {
        $push = new SendiosPush($this);
        $this->assertEquals(SendiosPush::CATEGORY_TRIGGER, $push->getCategoryTrigger());
    }
}

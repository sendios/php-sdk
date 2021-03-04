<?php

namespace Tests;

use Sendios\Http\Request;
use Sendios\Resources\Push;
use Sendios\SendiosSdk;
use PHPUnit\Framework\TestCase;
use Sendios\Services\Encrypter;
use Sendios\Services\ErrorHandler;

class PushTest extends TestCase
{
    public function testShouldCheckSend()
    {
        $predefinedResult = ['data' => 'ok'];
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';

        $data = array( // Data for letter
            'some' => 'hi',
            'letter' => 'John',
            'variables' => '!',
        );
        $meta = array(); // Your additional data

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->will($this->returnValue($predefinedResult));

        $sendios = new SendiosSdk($clientId, $clientKey);
        $sendios->request = $request;

        $sendios->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
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


        $result = $sendios->push->send($params['type_id'], $params['category'], $params['project_id'], $params['user']['email'], array('email' => ''), $data, $meta);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testCheckGetCategoryTrigger()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);
        $result = $sendios->push->getCategoryTrigger();
        $this->assertEquals(2, $result);
    }
}

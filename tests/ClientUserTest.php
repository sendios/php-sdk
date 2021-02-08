<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Sendios\Http\Request;
use Sendios\SendiosSdk;

class ClientUserTest extends TestCase
{
    public function testCreate()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $predefinedData = [
            'email' => 'test@mail.client-user',
            'project_id' => 1,
            'client_user_id' => 1,
        ];
        $sdk = new SendiosSdk($clientId, $clientKey);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with('clientuser/create', $predefinedData)
            ->will($this->returnValue(['status' => true]));

        $sdk->request = $request;
        $result = $sdk->clientUser->getUserFieldsByUser('test@mail.client-user', 1, 1);
        $this->assertEquals(['status' => true], $result);
    }
}
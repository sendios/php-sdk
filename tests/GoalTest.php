<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Sendios\SendiosSdk;
use Sendios\Services\CurlRequest;

class GoalTest extends TestCase
{
    public function testCreateGoal()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $data = [
            [
                'email' => 'test@mail.test',
                'type' => 'type',
                'project_id' => 1,
                'mail_id' => 2
            ]
        ];

        $curlRequest = $this->getMockBuilder(CurlRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $curlRequest->expects($this->once())->method('execute')
            ->will($this->returnValue(json_encode(['result' => true])));
        $curlRequest->expects($this->once())->method('getInfo')
            ->will($this->returnValue(200));

        $sendios->request->setCurlRequest($curlRequest);
        $result = $sendios->goal->createGoal($data);
        $this->assertEquals(['goals_added' => 1], $result);
    }

    public function testCreateGoalRequestFailed()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $data = [
            [
                'email' => 'test@mail.test',
                'type' => 'type',
                'project_id' => 1,
                'mail_id' => 2
            ]
        ];

        $curlRequest = $this->getMockBuilder(CurlRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $curlRequest->expects($this->once())->method('execute')
            ->will($this->returnValue(json_encode(['result' => false])));
        $curlRequest->expects($this->once())->method('getInfo')
            ->will($this->returnValue(500));

        $sendios->request->setCurlRequest($curlRequest);
        $result = $sendios->goal->createGoal($data);
        $this->assertEquals(['goals_added' => 0, 'errors' => 'Sending data error!'], $result);
    }

    public function testCreateGoalInvalidData()
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $sendios = new SendiosSdk($clientId, $clientKey);

        $data = [['mail_id' => 'Invalid']];

        $result = $sendios->goal->createGoal($data);
        $this->assertEquals([
            'goals_added' => 0,
            [
                'error_messages' => [
                    'Parameter email is invalid',
                    'Parameter type is invalid',
                    'Parameter project_id is invalid',
                    'Parameter mail_id is invalid'
                ],
                'errorCode' => 409,
                'message' => 'Validation error',
                'goal_data' => 'Invalid'
            ]
        ], $result);
    }
}
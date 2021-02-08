<?php

namespace Tests;

use Sendios\Http\Request;
use Sendios\Resources\Unsub;
use Sendios\SendiosSdk;
use PHPUnit\Framework\TestCase;
use Sendios\Services\ErrorHandler;

class UnsubTest extends TestCase
{
    /**
     * @var SendiosSdk
     */
    protected $service;
    
    public function setUp(): void
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $this->service = new SendiosSdk($clientId, $clientKey);
    }

    public function testAddByFbl()
    {
        $predefinedResult = ['data' => 'ok'];
        $user = ['id' => 42];

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/' . $user['id'] . '/source/' . Unsub::SOURCE_FBL)
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $request;
        $result = $this->service->unsub->addByFbl($user);

        $this->assertEquals($predefinedResult, $result);
    }

    public function testAddByFblWrongUser()
    {
        $request = $this->createMock(Request::class);

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $request;
        $result = $this->service->unsub->addByFbl(false);

        $this->assertEquals(false, $result);
    }

    public function testAddByLink()
    {
        $predefinedResult = ['data' => 'ok'];
        $user = ['id' => 42];

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/' . $user['id'] . '/source/' . Unsub::SOURCE_LINK)
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $request;
        $result = $this->service->unsub->addByLink($user);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testAddByClient()
    {
        $predefinedResult = ['data' => 'ok'];
        $user = ['id' => 42];

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/' . $user['id'] . '/source/' . Unsub::SOURCE_CLIENT)
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $request;
        $result = $this->service->unsub->addByClient($user);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testAddBySettings()
    {
        $predefinedResult = ['data' => 'ok'];
        $user = ['id' => 42];

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/' . $user['id'] . '/source/' . Unsub::SOURCE_SETTINGS)
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $request;
        $result = $this->service->unsub->addBySettings($user);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testSubscribe()
    {
        $predefinedResult = ['data' => 'ok'];
        $user = ['id' => 42];

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('delete')
            ->with('unsub/' . $user['id'])
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $request;
        $result = $this->service->unsub->subscribe($user);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testSubscribeWrongUser()
    {
        $user = [];
        $result = $this->service->unsub->subscribe($user);
        $this->assertEquals(false, $result);
    }

    public function testIsUnsubByUser()
    {
        $predefinedResult = ['data' => 'ok'];
        $user = ['id' => 42];

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('receive')
            ->with('unsub/isunsub/' . $user['id'])
            ->will($this->returnValue($predefinedResult));
        $this->service->request = $request;

        $result = $this->service->unsub->isUnsubByUser($user);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testIsUnsubByUserWrongUser()
    {
        $result = $this->service->unsub->isUnsubByUser([]);
        $this->assertEquals(false, $result);
    }

    public function testIsUnsubByEmailAndProjectId()
    {
        $predefinedResult = ['data' => 'ok'];
        $request = $this->createMock(Request::class);
        $request->expects($this->exactly(2))
            ->method('receive')
            ->withConsecutive(['user/project/1/email/test@mail.unsub'], ['unsub/isunsub/' . 1])
            ->willReturnOnConsecutiveCalls(
                ['user' => ['id' => 1]],
                $predefinedResult
            );


        $this->service->request = $request;

        $result = $this->service->unsub->isUnsubByEmailAndProjectId('test@mail.unsub', 1);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testIsUnsubByEmailAndProjectIdWrongUser()
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('receive')
            ->with('user/project/1/email/test@mail.unsub')
            ->will($this->returnValue(false));


        $this->service->request = $request;

        $result = $this->service->unsub->isUnsubByEmailAndProjectId('test@mail.unsub', 1);
        $this->assertEquals(false, $result);
    }

    public function testUnsubByAdmin()
    {

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/admin/' . 1 . '/email/' . base64_encode('test@unsub.email'))
            ->will($this->returnValue(['data' => 'ok']));


        $this->service->request = $request;
        $result = $this->service->unsub->unsubByAdmin('test@unsub.email', 1);
        $this->assertEquals(['data' => 'ok'], $result);
    }

    public function testUnsubByAdminWrongParams()
    {
        $result = $this->service->unsub->unsubByAdmin('test@unsub.email', false);
        $this->assertEquals(['unsub' => false], $result);
    }

    public function testUnsubByAdminApiError()
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('create')
            ->with('unsub/admin/' . 1 . '/email/' . base64_encode('test@unsub.email'))
            ->will($this->returnValue(false));


        $this->service->request = $request;
        $result = $this->service->unsub->unsubByAdmin('test@unsub.email', 1);
        $this->assertEquals(['unsub' => false], $result);
    }

    public function testGetUnsubscribeReason()
    {
        $predefinedResult = ['data' => 'ok'];

        $request = $this->createMock(Request::class);
        $request->expects($this->exactly(2))
            ->method('receive')
            ->withConsecutive(['user/project/1/email/test@mail.unsub'], ['unsub/unsubreason/' . 1])
            ->willReturnOnConsecutiveCalls(
                ['user' => ['id' => 1]],
                $predefinedResult
            );


        $this->service->request = $request;

        $result = $this->service->unsub->getUnsubscribeReason('test@mail.unsub', 1);
        $this->assertEquals($predefinedResult, $result);
    }

    public function testGetUnsubscribeReasonWrongUser()
    {

        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('receive')
            ->with('user/project/1/email/test@mail.unsub')
            ->will($this->returnValue(false));


        $this->service->request = $request;

        $result = $this->service->unsub->getUnsubscribeReason('test@mail.unsub', 1);
        $this->assertEquals(false, $result);
    }

    public function testGetByDate()
    {
        $request = $this->createMock(Request::class);
        $date = '2020-20-20';
        $request->expects($this->once())
            ->method('receive')
            ->with("unsub/list/" . strtotime($date))
            ->will($this->returnValue(['data' => 'ok']));

        $this->service->request = $request;
        $result = $this->service->unsub->getByDate($date);
        $this->assertEquals(['data' => 'ok'], $result);
    }
}

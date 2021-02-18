<?php

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sendios\Exception\ValidationException;
use Sendios\Http\Request;
use Sendios\SendiosSdk;
use Sendios\Services\ErrorHandler;

class WebPushTest extends TestCase
{
    /**
     * @var SendiosSdk
     */
    protected $service;

    /**
     * @var MockObject
     */
    protected $request;

    protected function setUp(): void
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $this->service = new SendiosSdk($clientId, $clientKey);
        $this->request = $this->createMock(Request::class);
    }

    public function testUnsubscribeByUser()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/user/get/1')
            ->will($this->returnValue(['result' => ['id' => 123]]));

        $this->request->expects($this->once())
            ->method('create')
            ->with('webpush/unsubscribe/123')
            ->will($this->returnValue(['result' => ['unsub' => true]]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->unsubscribeByUser(1);
        $this->assertEquals(['unsub' => true], $result);
    }

    public function testUnsubscribeByUserWrongUserData()
    {
        $result = $this->service->webpush->unsubscribeByUser(0);
        $this->assertEquals(false, $result);
    }

    public function testUnsubscribeByPushUser()
    {
        $this->request->expects($this->once())
            ->method('create')
            ->with('webpush/unsubscribe/1')
            ->will($this->returnValue(['result' => ['unsub' => true]]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->unsubscribeByPushUser(1);
        $this->assertEquals(['unsub' => true], $result);
    }

    public function testUnsubscribeByPushUserWrongUserData()
    {
        $result = $this->service->webpush->unsubscribeByPushUser(false);
        $this->assertEquals(false, $result);
    }

    public function testUnsubscribeByProjectIdAndHash()
    {
        $hash = md5('q');
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/project/get/1/hash/' . $hash)
            ->will($this->returnValue(['result' => ['id' => 123]]));

        $this->request->expects($this->once())
            ->method('create')
            ->with('webpush/unsubscribe/123')
            ->will($this->returnValue(['result' => ['unsub' => true]]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->unsubscribeByProjectIdAndHash(1, $hash);
        $this->assertEquals(['unsub' => true], $result);
    }

    public function testUnsubscribeByProjectIdAndHashNoUserFound()
    {
        $hash = md5('q');
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/project/get/1/hash/' . $hash)
            ->will($this->returnValue([]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->unsubscribeByProjectIdAndHash(1, $hash);
        $this->assertEquals(false, $result);
    }

    public function testSubscribeByUser()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/user/get/' . 1)
            ->will($this->returnValue(['result' => ['id' => 123]]));

        $this->request->expects($this->once())
            ->method('delete')
            ->with('webpush/subscribe/' . 123)
            ->will($this->returnValue(['result' => ['sub' => true]]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->subscribeByUser(1);
        $this->assertEquals(['sub' => true], $result);
    }

    public function testSubscribeByUserNotFound()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/user/get/' . 1)
            ->will($this->returnValue([]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->subscribeByUser(1);
        $this->assertEquals(false, $result);
    }

    public function testSubscribeByHash()
    {
        $hash = md5('q');
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/project/get/1/hash/' . $hash)
            ->will($this->returnValue(['result' => ['id' => 123]]));

        $this->request->expects($this->once())
            ->method('delete')
            ->with('webpush/subscribe/123')
            ->will($this->returnValue(['result' => ['sub' => true]]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->subscribeByHash(1, $hash);
        $this->assertEquals(['sub' => true], $result);
    }

    public function testSubscribeByHashUserNotFound()
    {
        $hash = md5('q');
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/project/get/1/hash/' . $hash)
            ->will($this->returnValue([]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->subscribeByHash(1, $hash);
        $this->assertEquals(false, $result);
    }

    public function testSendByUser()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/user/get/123')
            ->will($this->returnValue(['result' => ['id' => 123]]));


        $this->request->expects($this->once())
            ->method('create')
            ->with('webpush/send',
                [
                    'push_user_id' => 123,
                    'title' => 'title',
                    'url' => 'url',
                    'icon' => 'icon',
                    'type_id' => 1,
                    'meta' => [],
                    'text' => 'text',
                    'image_url' => null,
                ])
            ->will($this->returnValue(['result' => ['sent' => true]]));

        $this->service->request = $this->request;
        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $result = $this->service->webpush->sendByUser(123, 'title', 'text', 'url', 'icon', 1);
        $this->assertEquals(['sent' => true], $result);
    }

    public function testSendByUserNotFoundException()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/user/get/123')
            ->will($this->returnValue(false));


        $this->service->request = $this->request;
        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->expectException(ValidationException::class);
        $result = $this->service->webpush->sendByUser(123, 'title', 'text', 'url', 'icon', 1);
        $this->assertEquals(false, $result);
    }

    public function testSendByUserNotFoundWithoutException()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/user/get/123')
            ->will($this->returnValue(false));


        $this->service->request = $this->request;
        $result = $this->service->webpush->sendByUser(123, 'title', 'text', 'url', 'icon', 1);
        $this->assertEquals(false, $result);
    }

    public function testSendByProjectIdAndHash()
    {
        $hash = md5('test');
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/project/get/' . 1 . '/hash/' . $hash)
            ->will($this->returnValue(['result' => ['id' => 123]]));


        $this->request->expects($this->once())
            ->method('create')
            ->with('webpush/send',
                [
                    'push_user_id' => 123,
                    'title' => 'title',
                    'url' => 'url',
                    'icon' => 'icon',
                    'type_id' => 1,
                    'meta' => [],
                    'text' => 'text',
                    'image_url' => null,
                ])
            ->will($this->returnValue(['result' => ['sent' => true]]));

        $this->service->request = $this->request;
        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $result = $this->service->webpush->sendByProjectIdAndHash(1, $hash, 'title', 'text', 'url', 'icon', 1);
        $this->assertEquals(['sent' => true], $result);
    }

    public function testSendByProjectIdAndHashNotFoundException()
    {
        $hash = md5('q');
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/project/get/' . 1 . '/hash/' . $hash)
            ->will($this->returnValue([]));


        $this->service->request = $this->request;
        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->expectException(ValidationException::class);
        $this->service->webpush->sendByProjectIdAndHash(1, $hash, 'title', 'text', 'url', 'icon', 1);
    }

    public function testSendByProjectIdAndHashNotFoundWithoutException()
    {
        $hash = md5('q');
        $this->request->expects($this->once())
            ->method('receive')
            ->with('webpush/project/get/' . 1 . '/hash/' . $hash)
            ->will($this->returnValue([]));


        $this->service->request = $this->request;
        $result = $this->service->webpush->sendByProjectIdAndHash(1, $hash, 'title', 'text', 'url', 'icon', 1);
        $this->assertEquals(false, $result);
    }

    public function testSendByProject()
    {
        $this->request->expects($this->once())
            ->method('create')
            ->with('webpush/send',
                [
                    'project_id' => 1,
                    'title' => 'title',
                    'url' => 'url',
                    'icon' => 'icon',
                    'type_id' => 1,
                    'meta' => [],
                    'text' => 'text',
                    'image_url' => null,
                ])
            ->will($this->returnValue(['result' => ['sent' => true]]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->sendByProject(1, 'title', 'text', 'url', 'icon', 1);
        $this->assertEquals(['sent' => true], $result);
    }

    public function testCreatePushUser()
    {
        $this->request->expects($this->once())
            ->method('create')
            ->with('webpush/project/' . 1,
                [
                    'user_id' => 123,
                    'meta' => [
                        'url' => 'url',
                        'public_key' => 'public_key',
                        'auth_token' => 'auth_token',
                    ],
                ])
            ->will($this->returnValue(['push_user_id' => 7]));

        $this->service->request = $this->request;
        $result = $this->service->webpush->createPushUser(['id' => 123, 'project_id' => 1], 'url', 'public_key', 'auth_token');
        $this->assertEquals(7, $result);
    }

    public function testCreatePushUserNoUser()
    {
        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->expectException(ValidationException::class);
        $this->service->webpush->createPushUser([], 'url', 'public_key', 'auth_token');
    }

    public function testCreatePushUserNoUserId()
    {
        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->expectException(ValidationException::class);
        $this->service->webpush->createPushUser(['test' => 123], 'url', 'public_key', 'auth_token');
    }

    public function testCreatePushUserNoProjectId()
    {
        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->expectException(ValidationException::class);
        $this->service->webpush->createPushUser(['id' => 123], 'url', 'public_key', 'auth_token');
    }

    public function testCreatePushUserNoUserWithoutException()
    {
        $result = $this->service->webpush->createPushUser([], 'url', 'public_key', 'auth_token');
        $this->assertEquals(false, $result);
    }

    public function testCreatePushUserNoUserIdWithoutException()
    {
        $result = $this->service->webpush->createPushUser(['test' => 123], 'url', 'public_key', 'auth_token');
        $this->assertEquals(false, $result);
    }

    public function testCreatePushUserNoProjectIdWithoutException()
    {
        $result = $this->service->webpush->createPushUser(['id' => 123], 'url', 'public_key', 'auth_token');
        $this->assertEquals(false, $result);
    }
}
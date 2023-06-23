<?php

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sendios\Http\Request;
use Sendios\Resources\User;
use Sendios\SendiosSdk;
use Sendios\Services\CurlRequest;
use Sendios\Services\ErrorHandler;

class UserTest extends TestCase
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

    public function testShouldCheckGetByEmail()
    {
        $predefinedResult = ['user' => ['id' => 42]];
        $projectId = 3;
        $email = 'someone@example.com';

        $this->request->expects($this->once())
            ->method('receive')
            ->with('user/project/' . $projectId . '/email/' . $email)
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $this->request;
        $result = $this->service->user->getByEmail($email, $projectId);
        $this->assertEquals($predefinedResult['user'], $result);
    }

    public function testShouldCheckGetById()
    {
        $predefinedResult = ['user' => ['id' => 42]];
        $userId = 42;

        $this->request->expects($this->once())
            ->method('receive')
            ->with('user/id/' . $userId)
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $this->request;
        $result = $this->service->user->getById($userId);
        $this->assertEquals($predefinedResult['user'], $result);
    }

    public function testShouldCheckResolveId()
    {
        $predefinedResult = ['user' => ['id' => 42]];
        $userId = 42;

        $this->request = $this->createMock(Request::class);
        $this->request->expects($this->once())
            ->method('receive')
            ->with('user/id/' . $userId)
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $this->request;
        $result = $this->service->user->resolve($userId);
        $this->assertEquals($predefinedResult['user'], $result);
    }

    public function testShouldCheckResolveArray()
    {
        $predefinedUser = array(
            'id' => 42,
            'name' => 'John'
        );

        $errorHandler = $this->createMock(ErrorHandler::class);

        $user = new User($errorHandler, $this->request);
        $this->assertEquals($predefinedUser, $user->resolve($predefinedUser));
    }

    public function testShouldCheckPlatforms()
    {
        $errorHandler = $this->createMock(ErrorHandler::class);
        $user = new User($errorHandler, $this->request);

        $this->assertEquals(User::PLATFORM_ANDROID, $user->getPlatformAndroid());
        $this->assertEquals(User::PLATFORM_IOS, $user->getPlatformIos());
        $this->assertEquals(User::PLATFORM_DESKTOP, $user->getPlatformDesktop());
        $this->assertEquals(User::PLATFORM_MOBILE, $user->getPlatformMobile());
        $this->assertEquals(User::PLATFORM_UNKNOWN, $user->getPlatformUnknown());
    }

    public function testShouldCheckSetUserFieldsByEmailAndProjectId()
    {
        $predefinedResult = true;
        $projectId = 42;
        $email = 'email@example.com';
        $data = ['field1' => 174, 'ctime' => time()];

        $this->request->expects($this->once())
            ->method('update')
            ->with('userfields/project/' . $projectId . '/emailhash/' . base64_encode($email), $data)
            ->will($this->returnValue($predefinedResult));

        $this->service->errorHandler->setErrorMode(ErrorHandler::MODE_EXCEPTION);
        $this->service->request = $this->request;
        $result = $this->service->user->setUserFieldsByEmailAndProjectId('email@example.com', $projectId, $data);
        $this->assertTrue($result);
    }

    public function testSetUserFieldsByUser()
    {
        $this->request->expects($this->once())
            ->method('update')
            ->with('userfields/user/123', ['name' => 'test'])
            ->will($this->returnValue(['updated' => true]));

        $this->service->request = $this->request;
        $result = $this->service->user->setUserFieldsByUser(['id' => 123], ['name' => 'test']);
        $this->assertEquals(['updated' => true], $result);
    }

    public function testUserFieldsByUserWrongUserData()
    {
        $result = $this->service->user->setUserFieldsByUser([], []);
        $this->assertEquals(false, $result);
    }

    public function testGetUserFieldsByEmailAndProjectId()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('userfields/project/1/email/test.user@gmail.com')
            ->will($this->returnValue(['name' => 'test']));

        $this->service->request = $this->request;
        $result = $this->service->user->getUserFieldsByEmailAndProjectId('test.user@gmail.com', 1);
        $this->assertEquals(['name' => 'test'], $result);
    }

    public function testGetUserFieldsByUser()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('userfields/user/123')
            ->will($this->returnValue(['name' => 'test']));

        $this->service->request = $this->request;
        $result = $this->service->user->getUserFieldsByUser(['id' => 123]);
        $this->assertEquals(['name' => 'test'], $result);
    }

    public function testGetUserFieldsByUserWrongUserData()
    {
        $result = $this->service->user->getUserFieldsByUser([]);
        $this->assertEquals(false, $result);
    }

    public function testSetOnlineByEmailAndProjectId()
    {
        $curlRequest = $this->getMockBuilder(CurlRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $curlRequest->expects($this->once())->method('execute')
            ->will($this->returnValue(json_encode(['result' => true])));
        $curlRequest->expects($this->once())->method('getInfo')
            ->will($this->returnValue(200));

        $this->service->request->setCurlRequest($curlRequest);
        $this->service->user->setOnlineByEmailAndProjectId(
            'test@test.com',
            1,
            new \DateTime()
        );
    }

    public function testSetOnlineByUser()
    {
        $curlRequest = $this->getMockBuilder(CurlRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $curlRequest->expects($this->once())->method('execute')
            ->will($this->returnValue(json_encode(['result' => true])));
        $curlRequest->expects($this->once())->method('getInfo')
            ->will($this->returnValue(200));

        $this->service->request->setCurlRequest($curlRequest);
        $this->service->user->setOnlineByUser(
            ['id' => 123],
            new \DateTime()
        );
    }

    public function testSetOnlineByUserWrongUserData()
    {
        $result = $this->service->user->setOnlineByUser(
            [],
            new \DateTime()
        );
        $this->assertEquals(false, $result);
    }

    /**
     * @return void
     * @throws \Exception
     * @deprecated
     */
    public function testAddPaymentByEmailAndProjectId()
    {
        $time = time();
        $this->request->expects($this->once())
            ->method('receive')
            ->with('user/project/1/email/test@test.com')
            ->will($this->returnValue(['user' => ['id' => 123]]));

        $this->request->expects($this->once())
            ->method('create')
            ->with('lastpayment',
                ['start_date' => $time,
                    'user_id' => 123,
                    'expire_date' => $time,
                    'payment_type' => 1,
                    'amount' => 100,
                    'mail_id' => 12345
                ])
            ->will($this->returnValue(['added' => true]));

        $this->service->request = $this->request;
        $result = $this->service->user->addPaymentByEmailAndProjectId(
            'test@test.com',
            1,
            $time,
            $time,
            10,
            1,
            100,
            12345
        );
        $this->assertEquals(['added' => true], $result);
    }

    /**
     * @return void
     * @throws \Exception
     * @deprecated
     */
    public function testAddPaymentByEmailWrongUserData()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('user/project/1/email/test@test.com')
            ->will($this->returnValue(false));

        $this->service->request = $this->request;
        $result = $this->service->user->addPaymentByEmailAndProjectId(
            'test@test.com',
            1,
            time(),
            time(),
            10,
            1,
            100,
            12345
        );
        $this->assertEquals(false, $result);
    }

    /**
     * @return void
     * @throws \Exception
     * @deprecated
     */
    public function testAddPaymentByUser()
    {
        $time = time();
        $this->request->expects($this->once())
            ->method('create')
            ->with(
                'lastpayment',
                [
                    'start_date' => $time,
                    'user_id' => 123,
                    'expire_date' => $time,
                    'payment_type' => 1,
                    'amount' => 100,
                ]
            )
            ->will($this->returnValue(['added' => true]));

        $this->service->request = $this->request;
        $result = $this->service->user->addPaymentByUser(['id' => 123], $time, $time, 10, 1, 100);
        $this->assertEquals(['added' => true], $result);
    }

    /**
     * @return void
     * @throws \Exception
     * @deprecated
     */
    public function testAddPaymentByUserWrongUserData()
    {
        $time = time();
        $result = $this->service->user->addPaymentByUser([], $time, $time, 10, 1, 100);
        $this->assertEquals(false, $result);
    }

    public function testCreatePaymentByEmailAndProjectId()
    {
        $time = time();
        $this->request->expects($this->once())
            ->method('receive')
            ->with('user/project/1/email/test@test.com')
            ->will($this->returnValue(['user' => ['id' => 123]]));

        $this->request->expects($this->once())
            ->method('create')
            ->with(
                'lastpayment',
                [
                    'start_date' => $time,
                    'user_id' => 123,
                    'expire_date' => $time,
                    'payment_type' => 1,
                    'amount' => 100,
                    'mail_id' => 12345,
                ]
            )
            ->will($this->returnValue(['added' => true]));

        $this->service->request = $this->request;
        $result = $this->service->user->createPaymentByEmailAndProjectId(
            'test@test.com',
            1,
            $time,
            $time,
            1,
            100,
            12345
        );
        $this->assertEquals(['added' => true], $result);
    }

    public function testCreatePaymentByEmailWrongUserData()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('user/project/1/email/test@test.com')
            ->will($this->returnValue(false));

        $this->service->request = $this->request;
        $result = $this->service->user->createPaymentByEmailAndProjectId(
            'test@test.com',
            1,
            time(),
            time(),
            1,
            100,
            12345
        );
        $this->assertEquals(false, $result);
    }

    public function testCreatePaymentByUser()
    {
        $time = time();
        $this->request->expects($this->once())
            ->method('create')
            ->with(
                'lastpayment',
                [
                    'start_date' => $time,
                    'user_id' => 123,
                    'expire_date' => $time,
                    'payment_type' => 1,
                    'amount' => 100,
                ]
            )
            ->will($this->returnValue(['added' => true]));

        $this->service->request = $this->request;
        $result = $this->service->user->createPaymentByUser(['id' => 123], $time, $time, 1, 100);
        $this->assertEquals(['added' => true], $result);
    }

    public function testCreatePaymentByUserWrongUserData()
    {
        $time = time();
        $result = $this->service->user->createPaymentByUser([], $time, $time, 1, 100);
        $this->assertEquals(false, $result);
    }

    public function testForceConfirmByEmailAndProject()
    {
        $curlRequest = $this->getMockBuilder(CurlRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $curlRequest->expects($this->any())->method('setOption');
        $curlRequest->expects($this->once())->method('execute')
            ->will($this->returnValue(json_encode(['result' => true])));
        $curlRequest->expects($this->once())->method('getInfo')
            ->will($this->returnValue(200));

        $this->service->request->setCurlRequest($curlRequest);
        $this->service->user->forceConfirmByEmailAndProject(
            'test@test.mail',
            1
        );
    }

    public function testErase(): void
    {
        $email = 'foo@bar.baz';
        $projectId = 123;

        $this->request->expects($this->once())
            ->method('send')
            ->with('user/erase', 'POST', [
                'email' => $email,
                'project_id' => $projectId,
                'force_delete' => false
            ])
            ->will($this->returnValue(['deleted' => true]));

        $this->service->request = $this->request;

        $result = $this->service->user->erase($email, $projectId);
        $this->assertEquals(['deleted' => true], $result);
    }
}

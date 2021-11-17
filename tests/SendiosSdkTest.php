<?php

namespace Tests;

use Sendios\Exception\WrongResourceRequestedException;
use Sendios\Http\Request;
use Sendios\Resources\Email;
use Sendios\Resources\Push;
use Sendios\Resources\Unsub;
use Sendios\Resources\User;
use Sendios\SendiosSdk;
use PHPUnit\Framework\TestCase;
use Sendios\Services\ErrorHandler;

class SendiosSdkTest extends TestCase
{
    public function testShouldCheckClientKeyEmptyValidation()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('clientKey cannot be empty');

        new SendiosSdk(1, '');
    }

    public function testShouldCheckPropertyTypes()
    {
        $sendios = new SendiosSdk(123, 'some key');
        $this->assertInstanceOf(ErrorHandler::class, $sendios->errorHandler);
        $this->assertInstanceOf(Request::class, $sendios->request);
        $this->assertInstanceOf(Push::class, $sendios->push);
        $this->assertInstanceOf(Email::class, $sendios->email);
        $this->assertInstanceOf(User::class, $sendios->user);
        $this->assertInstanceOf(Unsub::class, $sendios->unsub);
    }

    public function testRequestNonExistingService()
    {
        $this->expectException(WrongResourceRequestedException::class);
        $sendios = new SendiosSdk(123, 'some key');
        $sendios->qwe;
    }
}

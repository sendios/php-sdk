<?php

namespace Tests;

use Sendios\Sendios;
use Sendios\SendiosEmail;
use Sendios\SendiosErrorHandler;
use Sendios\SendiosPush;
use Sendios\SendiosRequest;
use Sendios\SendiosUnsub;
use Sendios\SendiosUser;
use PHPUnit\Framework\TestCase;

class SendiosTest extends TestCase
{
    public function testShouldCheckClientIdEmptyValidation()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('clientId cannot be empty');

        new Sendios('', '');
    }

    public function testShouldCheckClientKeyEmptyValidation()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('clientKey cannot be empty');

        new Sendios('1', '');
    }

    public function testShouldCheckClientKeyStringValidation()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('clientKey must be a string');

        new Sendios('1', ['lk3kljhow43']);
    }

    public function testShouldCheckPropertyTypes()
    {
        $sendios = new Sendios(123, 'some key');
        $this->assertInstanceOf(SendiosErrorHandler::class, $sendios->errorHandler);
        $this->assertInstanceOf(SendiosRequest::class, $sendios->request);
        $this->assertInstanceOf(SendiosPush::class, $sendios->push);
        $this->assertInstanceOf(SendiosEmail::class, $sendios->email);
        $this->assertInstanceOf(SendiosUser::class, $sendios->user);
        $this->assertInstanceOf(SendiosUnsub::class, $sendios->unsub);
    }
}

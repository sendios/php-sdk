<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Sendios\SendiosSdk;
use Sendios\Services\CurlRequest;
use Sendios\Services\ErrorHandler;

final class ErrorHandlerTest extends TestCase
{
    public function testErrorHandler(): void
    {
        $customErrorHandler = $this->createMock(ErrorHandler::class);
        $customErrorHandler->expects($this->once())->method('handle');

        $curlRequest = $this->createMock(CurlRequest::class);
        $curlRequest->expects($this->once())
            ->method('execute')
            ->willReturn(false);
        $curlRequest->expects($this->once())
            ->method('getInfo')
            ->willReturn(500);

        $sdk = new SendiosSdk(123, 'a1s2d3f4g5h6j7k8l');
        $sdk->setErrorHandler($customErrorHandler);
        $sdk->request->setCurlRequest($curlRequest);

        $sdk->email->validate(1, 'test@validate.email');
    }
}

<?php

namespace Sendios\Resources;

use Sendios\Http\Request;
use Sendios\Services\ErrorHandler;

abstract class Resource
{
    protected $errorHandler;

    protected $request;

    public function __construct(ErrorHandler $errorHandler, Request $request)
    {
        $this->errorHandler = $errorHandler;
        $this->request = $request;
    }
}
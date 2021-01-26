<?php

namespace Sendios;

use Sendios\Exception\ValidationException;

class SendiosBuying extends SendiosDi
{
    /**
     * @param $email
     * @return bool
     * @throws \Exception
     */
    public function getBuyingDecision($email)
    {
        if (!$email) {
            $this->errorHandler->handle(new ValidationException('Email must be set.'));
            return false;
        }

        $requestData = [
            'email' => $email
        ];

        return $this->request->create('buying/email', $requestData);
    }
}
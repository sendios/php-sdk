<?php

namespace Sendios\Resources;

use Sendios\Exception\ValidationException;

final class Buying extends Resource
{
    private const CREATE_BUYING_RESOURCE = 'buying/email';

    /**
     * @param string $email
     * @return bool|mixed
     * @throws \Exception
     */
    public function getBuyingDecision(string $email)
    {
        if (!$email) {
            $this->errorHandler->handle(new ValidationException('Email must be set.'));

            return false;
        }

        $requestData = [
            'email' => $email
        ];

        return $this->request->create(self::CREATE_BUYING_RESOURCE, $requestData);
    }
}

<?php

namespace Sendios\Resources;

final class User extends BaseResource
{
    private const GET_USER_BY_EMAIL_RESOURCE = 'user/project/:projectId/email/:email';
    private const GET_USER_BY_ID_RESOURCE = 'user/id/:userId';
    private const SET_USER_FIELDS_BY_EMAIL_HASH_AND_PROJECT_ID_RESOURCE = 'userfields/project/:projectId/emailhash/:emailHash';
    private const SET_USER_FIELDS_BY_USER_ID_RESOURCE = 'userfields/user/:userId';
    private const GET_USER_FIELDS_BY_EMAIL_AND_PROJECT_ID_RESOURCE = 'userfields/project/:projectId/email/:email';
    private const GET_USER_FIELDS_BY_USER_ID_RESOURCE = 'userfields/user/:userId';
    private const SET_ONLINE_BY_EMAIL_AND_PROJECT_ID_RESOURCE = 'users/project/:projectId/email/:emailHash/online';
    private const SET_ONLINE_BY_USER_ID_RESOURCE = 'users/:userId/online';
    private const CREATE_LAST_PAYMENT_RESOURCE = 'lastpayment';
    private const CONFIRM_BY_EMAIL_AND_PROJECT_ID_RESOURCE = 'users/project/:projectId/email/:emailHash/confirm';
    private const ERASE = 'users/erase';

    public const PLATFORM_UNKNOWN = 0;
    public const PLATFORM_DESKTOP = 1;
    public const PLATFORM_MOBILE = 2;
    public const PLATFORM_ANDROID = 3;
    public const PLATFORM_IOS = 4;

    /**
     * @param string $email
     * @param int $projectId
     * @return false|mixed
     * @throws \Exception
     */
    public function getByEmail(string $email, int $projectId)
    {
        $result = $this->request->receive(strtr(self::GET_USER_BY_EMAIL_RESOURCE, [
            ':projectId' => $projectId,
            ':email' => $email
        ]));

        return $result ? $result['user'] : false;
    }

    /**
     * @param int $userId
     * @return false|mixed
     * @throws \Exception
     */
    public function getById(int $userId)
    {
        $result = $this->request->receive(strtr(self::GET_USER_BY_ID_RESOURCE, [':userId' => $userId]));

        return $result ? $result['user'] : false;
    }

    /**
     * @param $user
     * @return array|false|mixed
     * @throws \Exception
     */
    public function resolve($user)
    {
        if (is_array($user)) {
            return $user;
        }
        if (is_int($user)) {
            return $this->getById($user);
        }

        return false;
    }

    public function getPlatformUnknown(): int
    {
        return self::PLATFORM_UNKNOWN;
    }

    public function getPlatformDesktop(): int
    {
        return self::PLATFORM_DESKTOP;
    }

    public function getPlatformMobile(): int
    {
        return self::PLATFORM_MOBILE;
    }

    public function getPlatformAndroid(): int
    {
        return self::PLATFORM_ANDROID;
    }

    public function getPlatformIos(): int
    {
        return self::PLATFORM_IOS;
    }

    /**
     * Set custom user fields by user email and project ID
     *
     * @param string $email
     * @param int $projectId
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function setUserFieldsByEmailAndProjectId(string $email, int $projectId, array $data)
    {
        $resource = strtr(self::SET_USER_FIELDS_BY_EMAIL_HASH_AND_PROJECT_ID_RESOURCE, [
            ':projectId' => $projectId,
            ':emailHash' => base64_encode($email)
        ]);

        return $this->request->update($resource, $data);
    }

    /**
     * Set custom user fields by user
     *
     * @param array $user
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function setUserFieldsByUser(array $user, array $data)
    {
        if (!$user || !$user['id']) {
            return false;
        }

        $resource = strtr(self::SET_USER_FIELDS_BY_USER_ID_RESOURCE, [
            ':userId' => $user['id'],
        ]);

        return $this->request->update($resource, $data);
    }

    /**
     * Get custom user fields by user email and project ID
     *
     * @param string $email
     * @param int $projectId
     * @return bool|mixed
     * @throws \Exception
     */
    public function getUserFieldsByEmailAndProjectId(string $email, int $projectId)
    {
        $resource = strtr(self::GET_USER_FIELDS_BY_EMAIL_AND_PROJECT_ID_RESOURCE, [
            ':projectId' => $projectId,
            ':email' => $email,
        ]);

        return $this->request->receive($resource);
    }

    /**
     * Get custom user fields by user
     *
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function getUserFieldsByUser(array $user)
    {
        if (!$user || !$user['id']) {
            return false;
        }

        $resource = strtr(self::GET_USER_FIELDS_BY_USER_ID_RESOURCE, [
            ':userId' => $user['id']
        ]);

        return $this->request->receive($resource);
    }

    /**
     * @param string $email
     * @param int $projectId
     * @param \DateTime $dateTime
     * @return bool|mixed
     */
    public function setOnlineByEmailAndProjectId(string $email, int $projectId, \DateTime $dateTime)
    {
        $data = [
            'timestamp' => $dateTime->format('U'),
            'project_id' => $projectId,
            'encoded_email' => base64_encode($email),
        ];
        $resource = strtr(self::SET_ONLINE_BY_EMAIL_AND_PROJECT_ID_RESOURCE, [
            ':emailHash' => base64_encode($email),
            ':projectId' => $projectId,
        ]);

        return $this->request->sendToApi3($resource, 'PUT', $data);
    }

    /**
     * @param array $user
     * @param \DateTime $dateTime
     * @return bool|mixed
     * @throws \Exception
     */
    public function setOnlineByUser(array $user, \DateTime $dateTime)
    {
        if (!$user || !$user['id']) {
            return false;
        }

        $data = [
            'timestamp' => $dateTime->format('U'),
            'user_id' => $user['id']
        ];

        $resource = strtr(self::SET_ONLINE_BY_USER_ID_RESOURCE, [':userId' => $user['id']]);

        return $this->request->sendToApi3($resource, 'PUT', $data);
    }

    /**
     * Add payment by user email and project ID
     *
     * @param string $email
     * @param int $projectId
     * @param int $startDate
     * @param int|null $expireDate
     * @param int|null $totalCount
     * @param int|null $paymentType
     * @param int|null $amount
     * @param int|null $mailId
     * @return bool|mixed
     * @throws \Exception
     * @deprecated Using createPaymentByEmailAndProjectId instead.
     */
    public function addPaymentByEmailAndProjectId(
        string $email,
        int $projectId,
        int $startDate,
        ?int $expireDate = null,
        ?int $totalCount = null,
        ?int $paymentType = null,
        ?int $amount = null,
        ?int $mailId = null
    ) {
        $user = $this->getByEmail($email, $projectId);
        if (empty($user) || !$user['id']) {
            return false;
        }
        return $this->createPayment($user, $startDate, $expireDate, $paymentType, $amount, $mailId);
    }

    /**
     * Create payment by user email and project ID
     *
     * @param string $email
     * @param int $projectId
     * @param int $startDate
     * @param int|null $expireDate
     * @param int|null $paymentType
     * @param int|null $amount
     * @param int|null $mailId
     * @return bool|mixed
     * @throws \Exception
     */
    public function createPaymentByEmailAndProjectId(
        string $email,
        int $projectId,
        int $startDate,
        ?int $expireDate = null,
        ?int $paymentType = null,
        ?int $amount = null,
        ?int $mailId = null
    ) {
        $user = $this->getByEmail($email, $projectId);
        if (empty($user) || !$user['id']) {
            return false;
        }
        return $this->createPayment($user, $startDate, $expireDate, $paymentType, $amount, $mailId);
    }

    /**
     * Add payment by user
     *
     * @param array $user
     * @param int $startDate
     * @param int|null $expireDate
     * @param int|null $totalCount
     * @param int|null $paymentType
     * @param int|null $amount
     * @param int|null $mailId
     * @return bool|mixed
     * @throws \Exception
     * @deprecated Using createPaymentByEmailAndProjectId instead.
     */
    public function addPaymentByUser(
        array $user,
        int $startDate,
        ?int $expireDate = null,
        ?int $totalCount = null,
        ?int $paymentType = null,
        ?int $amount = null,
        ?int $mailId = null
    ) {
        if (empty($user) || !$user['id']) {
            return false;
        }

        return $this->createPayment($user, $startDate, $expireDate, $paymentType, $amount, $mailId);
    }

    /**
     * Create payment by user
     *
     * @param array $user
     * @param int $startDate
     * @param int|null $expireDate
     * @param int|null $paymentType
     * @param int|null $amount
     * @param int|null $mailId
     * @return bool|mixed
     * @throws \Exception
     */
    public function createPaymentByUser(
        array $user,
        int $startDate,
        ?int $expireDate = null,
        ?int $paymentType = null,
        ?int $amount = null,
        ?int $mailId = null
    ) {
        if (empty($user) || !$user['id']) {
            return false;
        }

        return $this->createPayment($user, $startDate, $expireDate, $paymentType, $amount, $mailId);
    }

    /**
     * @param array $user
     * @param int $startDate
     * @param int|null $expireDate
     * @param int|null $paymentType
     * @param int|null $amount
     * @param int|null $mailId
     * @return bool|mixed
     * @throws \Exception
     */
    private function createPayment(
        array $user,
        int $startDate,
        ?int $expireDate,
        ?int $paymentType,
        ?int $amount,
        ?int $mailId
    ) {
        if (!$user || !$user['id']) {
            return false;
        }

        $data = [
            'start_date' => $startDate,
            'user_id' => $user['id'],
        ];

        if ($expireDate) {
            $data['expire_date'] = $expireDate;
        }

        if ($paymentType) {
            $data['payment_type'] = $paymentType;
        }

        if ($amount) {
            $data['amount'] = $amount;
        }

        if ($mailId) {
            $data['mail_id'] = $mailId;
        }

        return $this->request->create(self::CREATE_LAST_PAYMENT_RESOURCE, $data);
    }

    /**
     * @param string $email
     * @param int $projectId
     * @return bool|mixed
     */
    public function forceConfirmByEmailAndProject(string $email, int $projectId)
    {
        $data = [
            'last_reaction' => time(),
            'project_id' => $projectId,
            'encoded_email' => base64_encode($email),
        ];

        $resource = strtr(self::CONFIRM_BY_EMAIL_AND_PROJECT_ID_RESOURCE, [
            ':emailHash' => base64_encode($email),
            ':projectId' => $projectId,
        ]);

        return $this->request->sendToApi3($resource, 'PUT', $data);
    }

    public function erase(string $email, int $projectId)
    {
        $data = [
            'email' => $email,
            'project_id' => $projectId,
        ];

        return $this->request->send(self::ERASE, 'POST', $data);
    }
}

<?php

namespace Sendios;

class SendiosUser extends SendiosDi
{
    const PLATFORM_UNKNOWN = 0;
    const PLATFORM_DESKTOP = 1;
    const PLATFORM_MOBILE = 2;
    const PLATFORM_ANDROID = 3;
    const PLATFORM_IOS = 4;

    public function getByEmail($email, $projectId)
    {
        $result = $this->request->receive('user/project/' . $projectId . '/email/' . $email);
        return $result ? $result['user'] : false;
    }

    public function getById($userId)
    {
        $result = $this->request->receive('user/id/' . $userId);
        return $result ? $result['user'] : false;
    }

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

    public function getPlatformUnknown()
    {
        return self::PLATFORM_UNKNOWN;
    }

    public function getPlatformDesktop()
    {
        return self::PLATFORM_DESKTOP;
    }

    public function getPlatformMobile()
    {
        return self::PLATFORM_MOBILE;
    }

    public function getPlatformAndroid()
    {
        return self::PLATFORM_ANDROID;
    }

    public function getPlatformIos()
    {
        return self::PLATFORM_IOS;
    }

    /**
     * Set custom user fields by user email and project ID
     *
     * @param string $email
     * @param int $projectId
     * @param array $data ['fieldName' => 'fieldValue']
     * @return bool
     */
    public function setUserFieldsByEmailAndProjectId($email, $projectId, array $data)
    {
        $resource = strtr('userfields/project/:projectId/emailhash/:emailHash', [
            ':projectId' => (int)$projectId,
            ':emailHash' => base64_encode($email)
        ]);
        return $this->request->update($resource, $data);
    }

    /**
     * Set custom user fields by user
     *
     * @param array $user
     * @param array $data ['fieldName' => 'fieldValue']
     * @return bool
     */
    public function setUserFieldsByUser(array $user, array $data)
    {
        $user = $this->resolve($user);
        if (!$user || !$user['id']) {
            return false;
        }

        $resource = strtr('userfields/user/:userId', [
            ':userId' => $user['id'],
        ]);
        return $this->request->update($resource, $data);
    }

    /**
     * Get custom user fields by user email and project ID
     *
     * @param string $email
     * @param int $projectId
     * @return bool
     */
    public function getUserFieldsByEmailAndProjectId($email, $projectId)
    {
        $resource = strtr('userfields/project/:projectId/email/:email', [
            ':projectId' => $projectId,
            ':email' => $email,
        ]);
        return $this->request->receive($resource);
    }

    /**
     * Get custom user fields by user
     *
     * @param array $user
     * @return bool
     */
    public function getUserFieldsByUser(array $user)
    {
        $user = $this->resolve($user);
        if (!$user || !$user['id']) {
            return false;
        }

        $resource = strtr('userfields/user/:userId', [
            ':userId' => $user['id']
        ]);
        return $this->request->receive($resource);
    }

    public function setOnlineByEmailAndProjectId($email, $projectId, \DateTime $dateTime)
    {
        $data = [
            'timestamp' => $dateTime->format('U'),
            'project_id' => $projectId,
            'encoded_email' => base64_encode($email)
        ];
        $resource = strtr('users/project/:projectId/email/:emailHash/online', [
            ':emailHash' => base64_encode($email),
            ':projectId' => $projectId,
        ]);

        return $this->request->sendToApi3($resource, 'PUT', $data);
    }

    public function setOnlineByUser(array $user, \DateTime $dateTime)
    {
        $user = $this->resolve($user);

        if (!$user || !$user['id']) {
            return false;
        }

        $data = [
            'timestamp' => $dateTime->format('U'),
            'user_id' => $user['id']
        ];

        $resource = 'users/' . $user['id'] . '/online';

        return $this->request->sendToApi3($resource, 'PUT', $data);
    }

    /**
     * Add payment by user email and project ID
     *
     * @param $email
     * @param $projectId
     * @param $startDate
     * @param bool $expireDate
     * @param bool|int $totalCount
     * @param bool|int $paymentType
     * @param bool|int $amount
     * @return bool
     */
    public function addPaymentByEmailAndProjectId($email, $projectId, $startDate, $expireDate = false, $totalCount = false, $paymentType = false, $amount = false)
    {
        $user = $this->getByEmail($email, $projectId);
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
        if ($totalCount) {
            $data['total_count'] = $totalCount;
        }

        if ($paymentType) {
            $data['payment_type'] = $paymentType;
        }

        if ($amount) {
            $data['amount'] = $amount;
        }

        return $this->request->create('lastpayment', $data);
    }

    /**
     * Add payment by user
     *
     * @param array $user
     * @param $startDate
     * @param bool $expireDate
     * @param bool $totalCount
     * @param bool $paymentType
     * @param bool|int $amount
     * @return bool
     */
    public function addPaymentByUser(array $user, $startDate, $expireDate = false, $totalCount = false, $paymentType = false, $amount = false)
    {
        $user = $this->resolve($user);
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
        if ($totalCount) {
            $data['total_count'] = $totalCount;
        }

        if ($paymentType) {
            $data['payment_type'] = $paymentType;
        }

        if ($amount) {
            $data['amount'] = $amount;
        }

        return $this->request->create('lastpayment', $data);
    }

    public function forceConfirmByEmailAndProject($email, $projectId)
    {
        $data = [
            'last_reaction' => strtotime('now'),
            'project_id' => $projectId,
            'encoded_email' => base64_encode($email)
        ];

        $resource = strtr('users/project/:projectId/email/:emailHash/confirm', [
            ':emailHash' => base64_encode($email),
            ':projectId' => $projectId,
        ]);

        return $this->request->sendToApi3($resource, 'PUT', $data);
    }
}

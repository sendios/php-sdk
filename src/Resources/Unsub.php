<?php

namespace Sendios\Resources;

use Sendios\Http\Request;
use Sendios\Services\ErrorHandler;

final class Unsub extends Resource
{
    public const SOURCE_FBL = 2; // Abuse
    public const SOURCE_LINK = 4; // Link in email
    public const SOURCE_CLIENT = 8; // Any your reason
    public const SOURCE_SETTINGS = 9; // Settings on site

    private $userResource;

    public function __construct(User $user, ErrorHandler $errorHandler, Request $request)
    {
        $this->userResource = $user;
        parent::__construct($errorHandler, $request);
    }

    /**
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function addByFbl(array $user)
    {
        return $this->addUser($user, self::SOURCE_FBL);
    }

    /**
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function addByLink(array $user)
    {
        return $this->addUser($user, self::SOURCE_LINK);
    }

    /**
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function addByClient(array $user)
    {
        return $this->addUser($user, self::SOURCE_CLIENT);
    }

    /**
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function addBySettings(array $user)
    {
        return $this->addUser($user, self::SOURCE_SETTINGS);
    }

    /**
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function subscribe(array $user)
    {
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->delete('unsub/' . $user['id']);
    }

    /**
     * @param array $user
     * @param int $sourceId
     * @return bool|mixed
     * @throws \Exception
     */
    protected function addUser(array $user, int $sourceId)
    {
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->create('unsub/' . $user['id'] . '/source/' . $sourceId);
    }

    /**
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function isUnsubByUser(array $user)
    {
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->receive('unsub/isunsub/' . $user['id']);
    }

    /**
     * @param string $email
     * @param int $projectId
     * @return bool|mixed
     * @throws \Exception
     */
    public function isUnsubByEmailAndProjectId(string $email, int $projectId)
    {
        $user = $this->userResource->getByEmail($email, $projectId);
        if (!$user || !$user['id']) {
            return false;
        }

        return $this->request->receive('unsub/isunsub/' . $user['id']);
    }

    /**
     * @param string $email
     * @param int $projectId
     * @return bool|false[]|mixed
     * @throws \Exception
     */
    public function unsubByAdmin(string $email, int $projectId)
    {
        if (!$email || !$projectId) {
            return ['unsub' => false];
        }

        $result = $this->request->create('unsub/admin/' . $projectId . '/email/' . base64_encode($email));
        if ($result === false) {
            return ['unsub' => false];
        }

        return $result;
    }

    /**
     * @param string $email
     * @param int $projectId
     * @return bool|mixed
     * @throws \Exception
     */
    public function getUnsubscribeReason(string $email, int $projectId)
    {
        $user = $this->userResource->getByEmail($email, $projectId);
        if (!$user || !$user['id']) {
            return false;
        }

        return $this->request->receive('unsub/unsubreason/' . $user['id']);
    }

    /**
     * @param string $date
     * @return bool|mixed
     * @throws \Exception
     */
    public function getByDate(string $date)
    {
        return $this->request->receive("unsub/list/" . strtotime($date));
    }
}

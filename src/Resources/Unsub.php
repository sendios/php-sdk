<?php

namespace Sendios\Resources;

use Sendios\Http\Request;
use Sendios\Services\ErrorHandler;

final class Unsub extends Resource
{
    const SOURCE_FBL = 2; // Abuse
    const SOURCE_LINK = 4; // Link in email
    const SOURCE_CLIENT = 8; // Any your reason
    const SOURCE_SETTINGS = 9; // Settings on site

    private $userResource;

    public function __construct(User $user, ErrorHandler $errorHandler, Request $request)
    {
        $this->userResource = $user;
        parent::__construct($errorHandler, $request);
    }

    public function addByFbl($user)
    {
        return $this->addUser($user, self::SOURCE_FBL);
    }

    public function addByLink($user)
    {
        return $this->addUser($user, self::SOURCE_LINK);
    }

    public function addByClient($user)
    {
        return $this->addUser($user, self::SOURCE_CLIENT);
    }

    public function addBySettings($user)
    {
        return $this->addUser($user, self::SOURCE_SETTINGS);
    }

    public function subscribe($user)
    {
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->delete('unsub/' . $user['id']);
    }

    protected function addUser($user, $sourceId)
    {
        $user = $this->userResource->resolve($user);
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->create('unsub/' . $user['id'] . '/source/' . $sourceId);
    }

    public function isUnsubByUser(array $user)
    {
        $user = $this->userResource->resolve($user);
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->receive('unsub/isunsub/' . $user['id']);
    }

    public function isUnsubByEmailAndProjectId($email, $projectId)
    {
        $user = $this->userResource->getByEmail($email, $projectId);
        if (!$user || !$user['id']) {
            return false;
        }

        return $this->request->receive('unsub/isunsub/' . $user['id']);
    }

    public function unsubByAdmin($email, $projectId)
    {
        if (!$email || !$projectId) {
            return ['unsub' => false];
        }

        $result = $this->request->create('unsub/admin/' . $projectId . '/email/' . base64_encode($email));
        if ($result == false) {
            return ['unsub' => false];
        }
        return $result;

    }

    public function getUnsubscribeReason($email, $projectId)
    {
        $user = $this->userResource->getByEmail($email, $projectId);
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->receive('unsub/unsubreason/' . $user['id']);
    }

    public function getByDate($date)
    {
        return $this->request->receive("unsub/list/" . strtotime($date));
    }
}

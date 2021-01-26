<?php

namespace Sendios;

class SendiosUnsub extends SendiosDi
{
    const SOURCE_FBL = 2; // Abuse
    const SOURCE_LINK = 4; // Link in email
    const SOURCE_CLIENT = 8; // Any your reason
    const SOURCE_SETTINGS = 9; // Settings on site

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
        $user = $this->user->resolve($user);
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->create('unsub/' . $user['id'] . '/source/' . $sourceId);
    }

    public function isUnsubByUser(array $user)
    {
        $user = $this->user->resolve($user);
        if (!$user || !$user['id']) {
            return false;
        }
        return $this->request->receive('unsub/isunsub/' . $user['id']);
    }

    public function isUnsubByEmailAndProjectId($email, $projectId)
    {
        $user = $this->user->getByEmail($email, $projectId);
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
        $user = $this->user->getByEmail($email, $projectId);
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

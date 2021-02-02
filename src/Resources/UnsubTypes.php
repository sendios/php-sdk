<?php

namespace Sendios\Resources;

final class UnsubTypes extends Resource
{
    /**
     * @param array|int $user
     * @return bool
     */
    public function getList($user)
    {
        if (is_int($user)) {
            $user = ['id' => $user];
        }

        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->receive('unsubtypes/' . $user['id']);
    }

    /**
     * @param array|int $user
     * @param array $typeIds
     */
    public function setDisabledTypes($user, array $typeIds)
    {
        if (is_int($user)) {
            $user = ['id' => $user];
        }

        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->create('unsubtypes/' . $user['id'], ['type_ids' => $typeIds]);
    }

    /**
     * @param array|int $user
     * @param array $typeIds
     */
    public function addTypes($user, array $typeIds)
    {
        if (is_int($user)) {
            $user = ['id' => $user];
        }

        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->create('unsubtypes/nodiff/' . $user['id'], ['type_ids' => $typeIds]);
    }

    /**
     * @param array|int $user
     * @param array $typeIds
     */
    public function removeTypes($user, array $typeIds)
    {
        if (is_int($user)) {
            $user = ['id' => $user];
        }

        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->delete('unsubtypes/nodiff/' . $user['id'], ['type_ids' => $typeIds]);
    }

    /**
     * @param array|int $user
     * @param array $typeIds
     */
    public function removeAll($user)
    {
        if (is_int($user)) {
            $user = ['id' => $user];
        }

        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->delete('unsubtypes/all/' . $user['id']);
    }

}

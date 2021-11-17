<?php

namespace Sendios\Resources;

final class UnsubTypes extends BaseResource
{
    /**
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function getList(array $user)
    {
        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->receive('unsubtypes/' . $user['id']);
    }

    /**
     * @param array $user
     * @param array $typeIds
     * @return bool|mixed
     * @throws \Exception
     */
    public function setDisabledTypes(array $user, array $typeIds)
    {
        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->create('unsubtypes/' . $user['id'], ['type_ids' => $typeIds]);
    }

    /**
     * @param array $user
     * @param array $typeIds
     * @return bool|mixed
     * @throws \Exception
     */
    public function addTypes(array $user, array $typeIds)
    {
        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->create('unsubtypes/nodiff/' . $user['id'], ['type_ids' => $typeIds]);
    }

    /**
     * @param array $user
     * @param array $typeIds
     * @return bool|mixed
     * @throws \Exception
     */
    public function removeTypes(array $user, array $typeIds)
    {
        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->delete('unsubtypes/nodiff/' . $user['id'], ['type_ids' => $typeIds]);
    }

    /**
     * @param array $user
     * @return bool|mixed
     * @throws \Exception
     */
    public function removeAll(array $user)
    {
        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        return $this->request->delete('unsubtypes/all/' . $user['id']);
    }
}

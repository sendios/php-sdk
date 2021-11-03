<?php

namespace Sendios\Resources;

final class ClientUser extends BaseResource
{
    private const CREATE_CLIENT_USER_RESOURCE = 'clientuser/create';

    /**
     * @param string $email
     * @param int $projectId
     * @param string $clientUserId
     * @return bool|mixed
     * @throws \Exception
     */
    public function create(string $email, int $projectId, string $clientUserId)
    {
        $data = [
            'email' => $email,
            'project_id' => $projectId,
            'client_user_id' => $clientUserId,
        ];

        return $this->request->create(self::CREATE_CLIENT_USER_RESOURCE, $data);
    }

    /**
     * @param string $email
     * @param int $projectId
     * @param string $clientUserId
     * @return bool|mixed
     * @throws \Exception
     */
    public function getUserFieldsByUser(string $email, int $projectId, string $clientUserId)
    {
        return $this->create($email, $projectId, $clientUserId);
    }
}

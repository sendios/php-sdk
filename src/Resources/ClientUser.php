<?php

namespace Sendios\Resources;

final class ClientUser extends Resource
{
    private const CREATE_CLIENT_USER_RESOURCE = 'clientuser/create';

    public function create($email, $projectId, $clientUserId)
    {
        $data = [
            'email' => $email,
            'project_id' => (int)$projectId,
            'client_user_id' => (int)$clientUserId,
        ];
        return $this->request->create(self::CREATE_CLIENT_USER_RESOURCE, $data);
    }

    public function getUserFieldsByUser($email, $projectId, $clientUserId)
    {
        return $this->create($email, $projectId, $clientUserId);
    }

}

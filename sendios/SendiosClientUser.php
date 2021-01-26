<?php

namespace Sendios;

class SendiosClientUser extends SendiosDi
{

    public function create($email, $projectId, $clientUserId)
    {
        $data = [
            'email' => $email,
            'project_id' => (int)$projectId,
            'client_user_id' => (int)$clientUserId,
        ];
        return $this->request->create('clientuser/create', $data);
    }

    public function getUserFieldsByUser($email, $projectId, $clientUserId)
    {
        return $this->create($email, $projectId, $clientUserId);
    }

}

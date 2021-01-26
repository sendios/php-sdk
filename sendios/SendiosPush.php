<?php

namespace Sendios;

class SendiosPush extends SendiosDi
{
    const CATEGORY_SYSTEM = 1;
    const CATEGORY_TRIGGER = 2;

    private $resources = array(
        self::CATEGORY_SYSTEM => 'push/system',
        self::CATEGORY_TRIGGER => 'push/trigger',
    );

    public function send($typeId, $categoryId, $projectId, $email, $user = array(), $data = array(), $meta = array())
    {
        $user['email'] = $email;
        $params = array(
            'type_id' => $typeId,
            'category' => $categoryId,
            'client_id' => $this->clientId,
            'project_id' => $projectId,
            'user' => $user,
            'meta' => $meta
        );

        $params['value_encrypt']['template_data'] = $this->di->encrypter->encrypt($data);

        $resource = $this->resources[$categoryId];

        return $this->request->create($resource, $params);
    }

    public function getCategorySystem()
    {
        return self::CATEGORY_SYSTEM;
    }

    public function getCategoryTrigger()
    {
        return self::CATEGORY_TRIGGER;
    }
}

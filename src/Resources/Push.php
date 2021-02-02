<?php

namespace Sendios\Resources;

use Sendios\Http\Request;
use Sendios\Services\Encrypter;
use Sendios\Services\ErrorHandler;

final class Push extends Resource
{
    private $clientId;

    private $encrypter;

    const CATEGORY_SYSTEM = 1;
    const CATEGORY_TRIGGER = 2;

    private $resources = array(
        self::CATEGORY_SYSTEM => 'push/system',
        self::CATEGORY_TRIGGER => 'push/trigger',
    );

    public function __construct($clientId, Encrypter $encrypter, ErrorHandler $errorHandler, Request $request)
    {
        $this->clientId = $clientId;
        $this->encrypter = $encrypter;
        parent::__construct($errorHandler, $request);
    }

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

        $params['value_encrypt']['template_data'] = $this->encrypter->encrypt($data);

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

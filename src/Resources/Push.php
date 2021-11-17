<?php

namespace Sendios\Resources;

use Sendios\Http\Request;
use Sendios\Services\Encrypter;
use Sendios\Services\ErrorHandler;

final class Push extends BaseResource
{
    /** @var int */
    private $clientId;

    /** @var Encrypter  */
    private $encrypter;

    private const CATEGORY_SYSTEM = 1;
    private const CATEGORY_TRIGGER = 2;

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

    /**
     * @param int $typeId
     * @param int $categoryId
     * @param int $projectId
     * @param string $email
     * @param array $user
     * @param array $data
     * @param array $meta
     * @return bool|mixed
     * @throws \Sendios\Exception\EncryptException
     * @throws \Exception
     */
    public function send(int $typeId, int $categoryId, int $projectId, string $email, array $user = [], array $data = [], array $meta = [])
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

    public function getCategorySystem(): int
    {
        return self::CATEGORY_SYSTEM;
    }

    public function getCategoryTrigger(): int
    {
        return self::CATEGORY_TRIGGER;
    }
}

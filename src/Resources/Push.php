<?php

namespace Sendios\Resources;

use Sendios\Http\Request;
use Sendios\Services\ErrorHandler;

final class Push extends BaseResource
{
    /** @var int */
    private $clientId;

    private const CATEGORY_SYSTEM = 1;
    private const CATEGORY_TRIGGER = 2;

    private $resources = array(
        self::CATEGORY_SYSTEM => 'push/system',
        self::CATEGORY_TRIGGER => 'push/trigger',
    );

    public function __construct($clientId, ErrorHandler $errorHandler, Request $request)
    {
        $this->clientId = $clientId;
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
     */public function send(int $typeId, int $categoryId, int $projectId, string $email, array $user = [], array $data = [], array $meta = [], ?int $templateId = null, ?int $subjectId = null)
    {
        $transactionalMailSettings = [];

        if ($subjectId) {
            $transactionalMailSettings['subject_id'] = $subjectId;
        }

        if ($templateId) {
            $transactionalMailSettings['template_id'] = $templateId;
        }

        $data['user'] = $user;
        $data['meta'] = $meta;

        $params = [
            'type_id' => $typeId,
            'project_id' => $projectId,
            'email' => $email,
            'data' => $data,
            'transactional_mail_settings' => $transactionalMailSettings,
        ];

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

<?php

namespace Sendios\Resources;

use Sendios\Exception\ValidationException;

final class Webpush extends Resource
{
    private const UNSUB_BY_USER_ID_RESOURCE = 'webpush/unsubscribe/:pushUserId';
    private const SUB_BY_USER_ID_RESOURCE = 'webpush/subscribe/:pushUserId';
    private const SEND_WEB_PUSH_RESOURCE = 'webpush/send';
    private const GET_WEB_PUSH_BY_USER_ID_RESOURCE = 'webpush/user/get/:userId';
    private const GET_WEB_PUSH_BY_PROJECT_ID_AND_HASH_RESOURCE = 'webpush/project/get/:projectId/hash/:hash';
    private const CREATE_WEB_PUSH_BY_PROJECT_ID_RESOURCE = 'webpush/project/:projectId';

    /**
     * @param $user
     * @return false|mixed
     * @throws \Exception
     */
    public function unsubscribeByUser($user)
    {
        $pushUser = $this->getPushUserByUser($user);
        if (!$pushUser || !$pushUser['id']) {
            return false;
        }

        $result = $this->request->create(strtr(self::UNSUB_BY_USER_ID_RESOURCE, [':pushUserId' => $pushUser['id']]));

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param int $pushUserId
     * @return false|mixed
     * @throws \Exception
     */
    public function unsubscribeByPushUser(int $pushUserId)
    {
        if (!$pushUserId) {
            return false;
        }

        $result = $this->request->create(strtr(self::UNSUB_BY_USER_ID_RESOURCE, [':pushUserId' => $pushUserId]));

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param $projectId
     * @param $hash
     * @return false|mixed
     * @throws \Exception
     */
    public function unsubscribeByProjectIdAndHash(int $projectId, string $hash)
    {
        $pushUser = $this->getPushUserByProjectIdAndHash($projectId, $hash);
        if (!$pushUser || !$pushUser['id']) {
            return false;
        }

        $result = $this->request->create(strtr(self::UNSUB_BY_USER_ID_RESOURCE, [':pushUserId' => $pushUser['id']]));

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param $user
     * @return false|mixed
     * @throws \Exception
     */
    public function subscribeByUser($user)
    {
        $pushUser = $this->getPushUserByUser($user);
        if (!$pushUser || !$pushUser['id']) {
            return false;
        }

        $result = $this->request->delete(strtr(self::SUB_BY_USER_ID_RESOURCE, [':pushUserId' => $pushUser['id']]));

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param int $projectId
     * @param string $hash
     * @return false|mixed
     * @throws \Exception
     */
    public function subscribeByHash(int $projectId, string $hash)
    {
        $pushUser = $this->getPushUserByProjectIdAndHash($projectId, $hash);
        if (!$pushUser || !$pushUser['id']) {
            return false;
        }

        $result = $this->request->delete(strtr(self::SUB_BY_USER_ID_RESOURCE, [':pushUserId' => $pushUser['id']]));

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param $user
     * @param string $title
     * @param string $text
     * @param string $url
     * @param string $iconUrl
     * @param int $typeId
     * @param array $meta
     * @param string|null $imageUrl
     * @return false|mixed
     * @throws \Exception
     */
    public function sendByUser($user, string $title, string $text, string $url, string $iconUrl, int $typeId, array $meta = [], ?string $imageUrl = null)
    {
        $pushUser = $this->getPushUserByUser($user);
        if (!$pushUser || !$pushUser['id']) {
            $this->errorHandler->handle(new ValidationException('PushUser was not found'));
            return false;
        }

        $webpushMessage = [
            'push_user_id' => $pushUser['id'],
            'title' => $title,
            'url' => $url,
            'icon' => $iconUrl,
            'type_id' => $typeId,
            'meta' => $meta,
            'text' => $text,
            'image_url' => $imageUrl,
        ];

        $result = $this->request->create(self::SEND_WEB_PUSH_RESOURCE, $webpushMessage);

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param int $projectId
     * @param string $hash
     * @param string $title
     * @param string $text
     * @param string $url
     * @param string $iconUrl
     * @param int $typeId
     * @param array $meta
     * @param string|null $imageUrl
     * @return false|mixed
     * @throws \Exception
     */
    public function sendByProjectIdAndHash(
        int $projectId,
        string $hash,
        string $title,
        string $text,
        string $url,
        string $iconUrl,
        int $typeId,
        array $meta = [],
        ?string $imageUrl = null
    ) {
        $pushUser = $this->getPushUserByProjectIdAndHash($projectId, $hash);
        if (empty($pushUser) || !$pushUser['id']) {
            $this->errorHandler->handle(new ValidationException('PushUser was not found'));

            return false;
        }

        $webpushMessage = [
            'push_user_id' => $pushUser['id'],
            'title' => $title,
            'url' => $url,
            'icon' => $iconUrl,
            'type_id' => $typeId,
            'meta' => $meta,
            'text' => $text,
            'image_url' => $imageUrl,
        ];

        $result = $this->request->create(self::SEND_WEB_PUSH_RESOURCE, $webpushMessage);

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param int $projectId
     * @param string $title
     * @param string $text
     * @param string $url
     * @param string $iconUrl
     * @param int $typeId
     * @param array $meta
     * @param string|null $imageUrl
     * @return false|mixed
     * @throws \Exception
     */
    public function sendByProject(
        int $projectId,
        string $title,
        string $text,
        string $url,
        string $iconUrl,
        int $typeId,
        array $meta = [],
        ?string $imageUrl = null
    ) {
        $webpushMessage = [
            'project_id' => $projectId,
            'title' => $title,
            'url' => $url,
            'icon' => $iconUrl,
            'type_id' => $typeId,
            'meta' => $meta,
            'text' => $text,
            'image_url' => $imageUrl,
        ];

        $result = $this->request->create(self::SEND_WEB_PUSH_RESOURCE, $webpushMessage);

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param $user
     * @return false|mixed
     * @throws \Exception
     */
    protected function getPushUserByUser($user)
    {
        if (is_int($user)) {
            $user = ['id' => $user];
        }

        if (!$user || !isset($user['id']) || !$user['id']) {
            return false;
        }

        $result = $this->request->receive(strtr(self::GET_WEB_PUSH_BY_USER_ID_RESOURCE, [':userId' => $user['id']]));

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param int $projectId
     * @param string $hash
     * @return false|mixed
     * @throws \Exception
     */
    protected function getPushUserByProjectIdAndHash(int $projectId, string $hash)
    {
        $result = $this->request->receive(strtr(self::GET_WEB_PUSH_BY_PROJECT_ID_AND_HASH_RESOURCE, [
            ':projectId' => $projectId,
            ':hash' => $hash
        ]));

        return !empty($result['result']) ? $result['result'] : false;
    }

    /**
     * @param array $user
     * @param string $url
     * @param string $publicKey
     * @param string $authToken
     * @return false|mixed
     * @throws \Exception
     */
    public function createPushUser(array $user, string $url, string $publicKey, string $authToken)
    {
        if (!$user) {
            $this->errorHandler->handle(new ValidationException('User was not found.'));
            return false;
        }
        if (!isset($user['id']) || !$user['id']) {
            $this->errorHandler->handle(new ValidationException('User id must be set.'));
            return false;
        }
        if (!isset($user['project_id']) || !$user['project_id']) {
            $this->errorHandler->handle(new ValidationException('Project id must be set.'));
            return false;
        }

        $userId = $user['id'];
        $projectId = $user['project_id'];

        $sendData = array(
            'user_id' => $userId,
            'meta' => array(
                'url' => $url,
                'public_key' => $publicKey,
                'auth_token' => $authToken,
            ),
        );

        $result = $this->request->create(strtr(self::CREATE_WEB_PUSH_BY_PROJECT_ID_RESOURCE, [':projectId' => $projectId]), $sendData);

        return !empty($result['push_user_id']) ? $result['push_user_id'] : false;
    }
}

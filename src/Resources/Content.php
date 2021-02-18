<?php

namespace Sendios\Resources;

use Sendios\Exception\ValidationException;

/**
 * @deprecated Not support this API point
 */
final class Content extends Resource
{
    private const TRACK_SHOW_RESOURCE = 'pushapp/content/show';

    /**
     * @param $project
     * @param $uid
     * @param null $entityId
     * @return bool
     * @throws \Exception
     */
    public function trackShow($project, $uid, $entityId = null)
    {
        if (!$project) {
            $this->errorHandler->handle(new ValidationException('Project must be set.'));
            return false;
        }
        if (!$uid) {
            $this->errorHandler->handle(new ValidationException('Uid must be set.'));
            return false;
        }
        if (!$entityId) {
            $this->errorHandler->handle(new ValidationException('Entity id must be set.'));
            return false;
        }

        return $this->request->sendToApi3(self::TRACK_SHOW_RESOURCE, 'POST', [
            'project' => $project, 'uid' => $uid, 'entity' => $entityId
        ]);
    }
}
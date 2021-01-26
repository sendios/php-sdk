<?php

namespace Sendios;

use Sendios\Exception\ValidationException;

class SendiosContent extends SendiosDi
{
    /**
     * @param $project
     * @param $uid
     * @param null $entityId
     * @return bool
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

        return $this->request->sendToApi3('pushapp/content/show', 'POST', [
            'project' => $project, 'uid' => $uid, 'entity' => $entityId
        ]);
    }
}
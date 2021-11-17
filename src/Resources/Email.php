<?php

namespace Sendios\Resources;

use Sendios\Exception\ValidationException;

final class Email extends BaseResource
{
    private const CHECK_EMAIL_RESOURCE = 'email/check';
    private const VALIDATE_EMAIL_RESOURCE = 'email/check/send';
    private const TRACK_CLICK_RESOURCE = 'trackemail/click/';

    /**
     * @param string $email
     * @param bool $sanitize
     * @return bool|mixed
     * @throws \Exception
     */
    public function check(string $email, $sanitize = true)
    {
        return $this->request->create(self::CHECK_EMAIL_RESOURCE, array(
            'email' => $email,
            'sanitize' => $sanitize));
    }

    /**
     * @param int $projectId
     * @param string $email
     * @return bool|mixed
     * @throws \Exception
     */
    public function validate(int $projectId, string $email)
    {
        return $this->request->create(self::VALIDATE_EMAIL_RESOURCE, array(
            'project' => $projectId,
            'email' => $email
        ));
    }

    /**
     * @param int $mailId
     * @return bool|mixed
     * @throws \Exception
     */
    public function trackClickByMailId(int $mailId)
    {
        if (filter_var($mailId, FILTER_VALIDATE_INT) === false) {
            $this->errorHandler->handle(new ValidationException('$mailId is not an integer'));

            return false;
        }

        return $this->request->create(self::TRACK_CLICK_RESOURCE . $mailId);
    }
}

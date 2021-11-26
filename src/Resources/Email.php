<?php

namespace Sendios\Resources;

use Sendios\Exception\ValidationException;

final class Email extends BaseResource
{
    private const CHECK_EMAIL_RESOURCE = 'email/check';
    private const VALIDATE_EMAIL_RESOURCE = 'email/check/send';
    private const TRACK_CLICK_RESOURCE = 'trackemail/click/';
    private const TRACK_MAIL_CLICK_RESOURCE = 'track/mail/click/client';

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

    /**
     * @param int $mailId
     * @return bool|mixed
     * @throws \Exception
     */
    public function trackMailClick(int $projectId, int $mailId, int $userId, int $type = null, int $source = null, int $typeId = null)
    {
        $resource = self::TRACK_MAIL_CLICK_RESOURCE . '?' . http_build_query(array_filter([
            'u' => $userId,
            'm' => $mailId,
            'p' => $projectId,
            't' => $typeId,
            's' => $source,
            'tp' => $type,
        ]));

        return $this->request->sendToApi3($resource, 'GET');
    }

    /**
     * @param int $mailId
     * @return bool|mixed
     * @throws \Exception
     */
    public function trackMailClickFromParams(string $params)
    {
        $paramsJson = base64_decode($params);
        $paramsArray = json_decode($paramsJson, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->errorHandler->handle(new ValidationException('Not validation params as json'));

            return false;
        }

        foreach (['u', 'm', 'p', 't', 's', 'tp'] as $item) {
            $paramsArray[$item] = $paramsArray[$item] ?? null;
            if (in_array($item, ['u', 'm', 'p']) && is_null($paramsArray[$item])) {
                $this->errorHandler->handle(new ValidationException('Params "' . $item . '" is not a null'));

                return false;
            }
        }

        return $this->trackMailClick(
            $paramsArray['p'],
            $paramsArray['m'],
            $paramsArray['u'],
            $paramsArray['t'],
            $paramsArray['s'],
            $paramsArray['tp']
        );
    }
}

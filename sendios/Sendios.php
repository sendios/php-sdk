<?php

namespace Sendios;

/**
 * Class Sendios
 *
 * @property SendiosErrorHandler $errorHandler
 * @property SendiosRequest $request
 * @property SendiosPush $push
 * @property SendiosEmail $email
 * @property SendiosUser $user
 * @property SendiosUnsub $unsub
 * @property SendiosUnsubTypes $unsubTypes
 * @property SendiosWebPush $webpush
 * @property SendiosGoal $goal
 * @property SendiosContent $content
 * @property SendiosProductEvent $event
 * @property SendiosClientUser $clientUser
 * @property Encrypter $encrypter
 */
class Sendios
{
    /**
     * @var int
     */
    public $clientId;

    /**
     * @var string
     */
    public $clientKey;

    /**
     * @var Encrypter
     */
    public $encrypter;

    public function __construct($clientId, $clientKey)
    {
        if (empty($clientId)) {
            throw new \InvalidArgumentException('clientId cannot be empty');
        }
        if (empty($clientKey)) {
            throw new \InvalidArgumentException('clientKey cannot be empty');
        }
        if (!is_string($clientKey)) {
            throw new \InvalidArgumentException('clientKey must be a string');
        }

        $this->clientId = $clientId;
        $this->clientKey = $clientKey;

        $this->errorHandler = new SendiosErrorHandler();
        $this->request = new SendiosRequest($this);
        $this->push = new SendiosPush($this);
        $this->email = new SendiosEmail($this);
        $this->user = new SendiosUser($this);
        $this->unsub = new SendiosUnsub($this);
        $this->unsubTypes = new SendiosUnsubTypes($this);
        $this->webpush = new SendiosWebPush($this);
        $this->goal = new SendiosGoal($this);
        $this->content = new SendiosContent($this);
        $this->event = new SendiosProductEvent($this);
        $this->clientUser = new SendiosClientUser($this);
        $this->buying = new SendiosBuying($this);
        $this->encrypter = $this->getEncrypter($this->clientKey);
    }

    /**
     * @param $clientKey
     * @return Encrypter
     * @throws Exception\EncryptException
     */
    protected function getEncrypter($clientKey): Encrypter
    {
        $hash = substr(md5($clientKey), 4, 16);

        return new Encrypter($hash);
    }
}

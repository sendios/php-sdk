<?php

namespace Sendios;

use Sendios\Exception\WrongResourceRequestedException;
use Sendios\Http\Request;
use Sendios\Resources\Buying;
use Sendios\Resources\ClientUser;
use Sendios\Resources\Email;
use Sendios\Resources\Push;
use Sendios\Resources\Unsub;
use Sendios\Resources\UnsubTypes;
use Sendios\Resources\User;
use Sendios\Resources\Webpush;
use Sendios\Services\Encrypter;
use Sendios\Services\ErrorHandler;

/**
 * Class SendiosSdk
 *
 * @property ErrorHandler $errorHandler
 * @property Request $request
 * @property Buying $buying
 * @property Push $push
 * @property Email $email
 * @property User $user
 * @property Unsub $unsub
 * @property UnsubTypes $unsubTypes
 * @property Webpush $webpush
 * @property ClientUser $clientUser
 * @property Encrypter $encrypter
 */
class SendiosSdk
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

    private const RESOURCES_PROPERTIES = ['email', 'user', 'unsub', 'unsubTypes', 'webpush', 'content', 'event', 'clientUser', 'buying', 'push'];

    /**
     * SendiosSdk constructor.
     * @param int $clientId
     * @param string $clientKey
     * @throws Exception\EncryptException
     */
    public function __construct(int $clientId, string $clientKey)
    {
        if (empty($clientId)) {
            throw new \InvalidArgumentException('clientId cannot be empty');
        }
        if (empty($clientKey)) {
            throw new \InvalidArgumentException('clientKey cannot be empty');
        }

        $this->clientId = $clientId;
        $this->clientKey = $clientKey;

        $this->errorHandler = new ErrorHandler();
        $this->encrypter = $this->getEncrypter($this->clientKey);
        $this->request = new Request($this->clientId, $this->clientKey, $this->errorHandler);
    }

    /**
     * Use this only for Resources access
     * @param $name
     * @return mixed
     * @throws WrongResourceRequestedException
     */
    public function __get(string $name)
    {
        if (!in_array($name, self::RESOURCES_PROPERTIES, true)) {
            throw new WrongResourceRequestedException("Requested property {$name} is not in resources list");
        }
        if (!property_exists($this, $name)) {
            switch ($name) {
                case 'push' : {
                    $this->push = new Push($this->clientId, $this->encrypter, $this->errorHandler, $this->request);
                    return $this->push;
                }
                case 'unsub' : {
                    $this->unsub = new Unsub($this->user, $this->errorHandler, $this->request);
                    return $this->unsub;
                }
                default : {
                    $className = 'Sendios\Resources\\' . ucfirst($name);
                    $this->{$name} = new $className($this->errorHandler, $this->request);
                    return $this->{$name};
                }
            }
        }

        return $this->{$name};
    }

    /**
     * @param string $clientKey
     * @return Encrypter
     * @throws Exception\EncryptException
     */
    protected function getEncrypter(string $clientKey): Encrypter
    {
        $hash = substr(md5($clientKey), 4, 16);

        return new Encrypter($hash);
    }
}

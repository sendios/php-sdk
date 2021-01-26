<?php

namespace Sendios;

/**
 * Class SendiosDi
 *
 * @property SendiosPush push
 * @property SendiosUser user
 * @property SendiosRequest request
 * @property SendiosErrorHandler errorHandler
 * @property int|string clientId
 * @property int|string clientKey
 *
 */
class SendiosDi
{
    public $di = null;

    public function __construct($di)
    {
        $this->di = $di;
    }

    public function __get($propertyName)
    {
        if (isset($this->di->{$propertyName})) {
            $this->{$propertyName} = &$this->di->{$propertyName};
            return $this->{$propertyName};
        } else {
            return null;
        }
    }
}

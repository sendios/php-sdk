<?php

namespace Sendios;

use Sendios\Exception\EncryptException;

class Encrypter
{
    /**
     * The algorithm used for encryption.
     * @var string
     */
    private $cipher;

    /**
     * The encryption key.
     * @var string
     */
    private $key;

    /**
     * Encrypter constructor.
     * @param $key
     * @param string $cipher
     * @throws EncryptException
     */
    public function __construct(string $key, string $cipher = 'AES-128-CBC')
    {
        if (static::supported($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new EncryptException('Incorrect key lengths.');
        }
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param string $key
     * @param string $cipher
     * @return bool
     */
    private static function supported(string $key, string $cipher): bool
    {
        $length = mb_strlen($key, '8bit');

        return ($cipher === 'AES-128-CBC' && $length === 16) || ($cipher === 'AES-256-CBC' && $length === 32);
    }

    /**
     * @param $value
     * @return string
     * @throws EncryptException
     * @throws \Exception
     */
    public function encrypt($value): string
    {
        $iv = random_bytes($this->getIvSize());

        $value = \openssl_encrypt(serialize($value), $this->cipher, $this->key, 0, $iv);

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        $mac = $this->hash($iv = base64_encode($iv), $value);

        $json = json_encode(compact('iv', 'value', 'mac'));

        if (!is_string($json)) {
            throw new EncryptException('Can\'t encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * @param $iv
     * @param $value
     * @return string
     */
    private function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv . $value, $this->key);
    }

    /**
     * @return int
     */
    private function getIvSize(): int
    {
        return 16;
    }
}

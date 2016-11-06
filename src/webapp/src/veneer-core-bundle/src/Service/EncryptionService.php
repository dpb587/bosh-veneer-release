<?php

namespace Veneer\CoreBundle\Service;

class EncryptionService
{
    protected $options;
    protected $iv;

    public function __construct(array $options = array())
    {
        $this->options = array_merge(
            array(
                'cipher' => MCRYPT_RIJNDAEL_256,
                'mode' => MCRYPT_MODE_ECB,
                'random' => MCRYPT_DEV_URANDOM,
                'key' => 'ThisTokenIsNotSoSecretChangeIt',
            ),
            $options
        );
    }

    protected function getIV()
    {
        if (null === $this->iv) {
            $this->iv = mcrypt_create_iv(
                mcrypt_get_iv_size(
                    $this->options['cipher'],
                    $this->options['mode']
                ),
                $this->options['random']
            );
        }

        return $this->iv;
    }

    public function encrypt($data, $salt = null)
    {
        return base64_encode(
            mcrypt_encrypt(
                $this->options['cipher'],
                md5($this->options['key'].$salt),
                base64_encode($data),
                $this->options['mode'],
                $this->getIV()
            )
        );
    }

    public function decrypt($data, $salt = null)
    {
        return base64_decode(
            mcrypt_decrypt(
                $this->options['cipher'],
                md5($this->options['key'].$salt),
                base64_decode($data),
                $this->options['mode'],
                $this->getIV()
            )
        );
    }
}

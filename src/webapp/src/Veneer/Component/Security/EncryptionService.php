<?php

namespace Veneer\Component\Security;

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
                'salt' => 'ThisTokenIsNotSoSecretChangeIt',
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

    public function encrypt($data, $pepper = null)
    {
        return base64_encode(
            mcrypt_encrypt(
                $this->options['cipher'],
                md5($this->options['salt'] . $pepper),
                $data,
                $this->options['mode'],
                $this->getIV()
            )
        );
    }

    public function decrypt($data, $pepper = null)
    {
        return mcrypt_decrypt(
            $this->options['cipher'],
            md5($this->options['salt'] . $pepper),
            base64_decode($data),
            $this->options['mode'],
            $this->getIV()
        );
    }
}
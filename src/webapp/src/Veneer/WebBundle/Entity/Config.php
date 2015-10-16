<?php

namespace Veneer\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="web_config")
 * @ORM\Entity
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\Column(name="config_key", type="string", length=128)
     */
    protected $key;

    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    /**
     * @ORM\Column(name="config_value", type="text")
     */
    protected $value;

    public function setValue($value)
    {
        $this->value = serialize($value);

        return $this;
    }

    public function getValue()
    {
        return unserialize($this->value);
    }
}

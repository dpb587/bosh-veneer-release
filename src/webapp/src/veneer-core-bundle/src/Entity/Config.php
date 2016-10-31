<?php

namespace Veneer\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * !ORM\Entity
 * !ORM\Table(name="web_config")
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

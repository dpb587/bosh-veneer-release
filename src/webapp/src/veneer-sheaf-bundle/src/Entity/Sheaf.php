<?php

namespace Veneer\SheafBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Veneer\HubBundle\Entity\ParsedSemverTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="sheaf_sheaf")
 */
class Sheaf
{
    use ParsedSemverTrait;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(name="sheaf", type="string")
     */
    protected $sheaf;

    public function setSheaf($sheaf)
    {
        $this->sheaf = $sheaf;

        return $this;
    }

    public function getSheaf()
    {
        return $this->sheaf;
    }

    /**
     * @ORM\Column(name="version", type="string")
     */
    protected $version;

    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }
}

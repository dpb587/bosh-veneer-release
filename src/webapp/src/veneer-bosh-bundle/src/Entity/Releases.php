<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Releases
 *
 * @ORM\Table(name="releases", uniqueConstraints={@ORM\UniqueConstraint(name="releases_name_key", columns={"name"})})
 * @ORM\Entity
 */
class Releases extends \Veneer\BoshBundle\Service\AbstractEntity
{
    protected $versions;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="releases_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

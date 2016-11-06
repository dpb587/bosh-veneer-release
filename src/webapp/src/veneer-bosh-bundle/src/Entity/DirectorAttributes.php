<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DirectorAttributes.
 *
 * @ORM\Table(name="director_attributes", uniqueConstraints={@ORM\UniqueConstraint(name="unique_attribute_name", columns={"name"})})
 * @ORM\Entity
 */
class DirectorAttributes extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    protected $value;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="director_attributes_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;
}

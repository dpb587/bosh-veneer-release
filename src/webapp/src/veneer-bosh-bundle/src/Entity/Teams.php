<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teams.
 *
 * @ORM\Table(name="teams", uniqueConstraints={@ORM\UniqueConstraint(name="teams_name_key", columns={"name"})})
 * @ORM\Entity
 */
class Teams extends \Veneer\BoshBundle\Service\AbstractEntity
{
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
     * @ORM\SequenceGenerator(sequenceName="teams_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Veneer\BoshBundle\Entity\Deployments", mappedBy="team")
     */
    protected $deployment;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Veneer\BoshBundle\Entity\Tasks", mappedBy="team")
     */
    protected $task;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->deployment = new \Doctrine\Common\Collections\ArrayCollection();
        $this->task = new \Doctrine\Common\Collections\ArrayCollection();
    }
}

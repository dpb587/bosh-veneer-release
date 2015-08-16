<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deployments
 *
 * @ORM\Table(name="deployments", uniqueConstraints={@ORM\UniqueConstraint(name="deployments_name_key", columns={"name"})}, indexes={@ORM\Index(name="IDX_373C43D5DD12E1A4", columns={"cloud_config_id"})})
 * @ORM\Entity
 */
class Deployments extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="manifest", type="text", nullable=true)
     */
    protected $manifest;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="deployments_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\CloudConfigs
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\CloudConfigs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cloud_config_id", referencedColumnName="id")
     * })
     */
    protected $cloudConfig;


}

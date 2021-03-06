<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeploymentProperties.
 *
 * @ORM\Table(name="deployment_properties", uniqueConstraints={@ORM\UniqueConstraint(name="deployment_properties_deployment_id_name_key", columns={"deployment_id", "name"})}, indexes={@ORM\Index(name="IDX_EAC48AF69DF4CE98", columns={"deployment_id"})})
 * @ORM\Entity
 */
class DeploymentProperties extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    protected $value;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="deployment_properties_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\Deployments
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Deployments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deployment_id", referencedColumnName="id")
     * })
     */
    protected $deployment;
}

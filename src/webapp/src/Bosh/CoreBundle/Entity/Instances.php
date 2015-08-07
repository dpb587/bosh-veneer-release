<?php

namespace Bosh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Instances
 *
 * @ORM\Table(name="instances", uniqueConstraints={@ORM\UniqueConstraint(name="instances_vm_id_key", columns={"vm_id"})}, indexes={@ORM\Index(name="IDX_7A2700699DF4CE98", columns={"deployment_id"})})
 * @ORM\Entity
 */
class Instances extends \Bosh\CoreBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="job", type="text", nullable=false)
     */
    protected $job;

    /**
     * @var integer
     *
     * @ORM\Column(name="index", type="integer", nullable=false)
     */
    protected $index;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="text", nullable=false)
     */
    protected $state;

    /**
     * @var boolean
     *
     * @ORM\Column(name="resurrection_paused", type="boolean", nullable=true)
     */
    protected $resurrectionPaused;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="instances_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Bosh\CoreBundle\Entity\Vms
     *
     * @ORM\ManyToOne(targetEntity="Bosh\CoreBundle\Entity\Vms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vm_id", referencedColumnName="id")
     * })
     */
    protected $vm;

    /**
     * @var \Bosh\CoreBundle\Entity\Deployments
     *
     * @ORM\ManyToOne(targetEntity="Bosh\CoreBundle\Entity\Deployments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deployment_id", referencedColumnName="id")
     * })
     */
    protected $deployment;


}

<?php

namespace Bosh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vms
 *
 * @ORM\Table(name="vms", uniqueConstraints={@ORM\UniqueConstraint(name="vms_agent_id_key", columns={"agent_id"})}, indexes={@ORM\Index(name="IDX_B662F89C9DF4CE98", columns={"deployment_id"})})
 * @ORM\Entity
 */
class Vms extends \Bosh\CoreBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="agent_id", type="text", nullable=false)
     */
    protected $agentId;

    /**
     * @var string
     *
     * @ORM\Column(name="cid", type="text", nullable=true)
     */
    protected $cid;

    /**
     * @var string
     *
     * @ORM\Column(name="apply_spec_json", type="text", nullable=true)
     */
    protected $applySpecJson;

    /**
     * @var string
     *
     * @ORM\Column(name="credentials_json", type="text", nullable=true)
     */
    protected $credentialsJson;

    /**
     * @var string
     *
     * @ORM\Column(name="env_json", type="text", nullable=true)
     */
    protected $envJson;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="vms_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

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

<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Instances
 *
 * @ORM\Table(name="instances", uniqueConstraints={@ORM\UniqueConstraint(name="instances_vm_id_key", columns={"vm_id"}), @ORM\UniqueConstraint(name="instances_uuid_key", columns={"uuid"}), @ORM\UniqueConstraint(name="instances_vm_cid_key", columns={"vm_cid"}), @ORM\UniqueConstraint(name="instances_agent_id_key", columns={"agent_id"})}, indexes={@ORM\Index(name="IDX_7A2700699DF4CE98", columns={"deployment_id"})})
 * @ORM\Entity
 */
class Instances extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @var string
     *
     * @ORM\Column(name="uuid", type="text", nullable=true)
     */
    protected $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="availability_zone", type="text", nullable=true)
     */
    protected $availabilityZone;

    /**
     * @var string
     *
     * @ORM\Column(name="cloud_properties", type="text", nullable=true)
     */
    protected $cloudProperties;

    /**
     * @var boolean
     *
     * @ORM\Column(name="compilation", type="boolean", nullable=true)
     */
    protected $compilation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="bootstrap", type="boolean", nullable=true)
     */
    protected $bootstrap;

    /**
     * @var string
     *
     * @ORM\Column(name="dns_records", type="text", nullable=true)
     */
    protected $dnsRecords;

    /**
     * @var string
     *
     * @ORM\Column(name="spec_json", type="text", nullable=true)
     */
    protected $specJson;

    /**
     * @var string
     *
     * @ORM\Column(name="vm_cid", type="text", nullable=true)
     */
    protected $vmCid;

    /**
     * @var string
     *
     * @ORM\Column(name="agent_id", type="text", nullable=true)
     */
    protected $agentId;

    /**
     * @var string
     *
     * @ORM\Column(name="credentials_json", type="text", nullable=true)
     */
    protected $credentialsJson;

    /**
     * @var string
     *
     * @ORM\Column(name="trusted_certs_sha1", type="text", nullable=true)
     */
    protected $trustedCertsSha1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="update_completed", type="boolean", nullable=true)
     */
    protected $updateCompleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ignore", type="boolean", nullable=true)
     */
    protected $ignore;

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
     * @var \Veneer\BoshBundle\Entity\Deployments
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Deployments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deployment_id", referencedColumnName="id")
     * })
     */
    protected $deployment;

    /**
     * @var \Veneer\BoshBundle\Entity\Vms
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Vms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vm_id", referencedColumnName="id")
     * })
     */
    protected $vm;


}

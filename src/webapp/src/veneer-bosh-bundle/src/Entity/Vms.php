<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vms
 *
 * @ORM\Table(name="vms", uniqueConstraints={@ORM\UniqueConstraint(name="vms_agent_id_key", columns={"agent_id"})})
 * @ORM\Entity
 */
class Vms extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @var integer
     *
     * @ORM\Column(name="deployment_id", type="integer", nullable=false)
     */
    protected $deploymentId;

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
     * @var string
     *
     * @ORM\Column(name="trusted_certs_sha1", type="text", nullable=true)
     */
    protected $trustedCertsSha1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="vms_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeploymentProblems
 *
 * @ORM\Table(name="deployment_problems", indexes={@ORM\Index(name="deployment_problems_deployment_id_type_state_index", columns={"deployment_id", "type", "state"}), @ORM\Index(name="deployment_problems_deployment_id_state_created_at_index", columns={"deployment_id", "state", "created_at"}), @ORM\Index(name="IDX_B104A72E9DF4CE98", columns={"deployment_id"})})
 * @ORM\Entity
 */
class DeploymentProblems extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="state", type="text", nullable=false)
     */
    protected $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="resource_id", type="integer", nullable=false)
     */
    protected $resourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", nullable=false)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="data_json", type="text", nullable=false)
     */
    protected $dataJson;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_seen_at", type="datetime", nullable=false)
     */
    protected $lastSeenAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="counter", type="integer", nullable=false)
     */
    protected $counter;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="deployment_problems_id_seq", allocationSize=1, initialValue=1)
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

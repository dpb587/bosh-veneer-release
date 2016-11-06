<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IpAddresses.
 *
 * @ORM\Table(name="ip_addresses", uniqueConstraints={@ORM\UniqueConstraint(name="ip_addresses_address_key", columns={"address"})}, indexes={@ORM\Index(name="IDX_F1D07E783A51721D", columns={"instance_id"})})
 * @ORM\Entity
 */
class IpAddresses extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="network_name", type="text", nullable=true)
     */
    protected $networkName;

    /**
     * @var int
     *
     * @ORM\Column(name="address", type="bigint", nullable=true)
     */
    protected $address;

    /**
     * @var bool
     *
     * @ORM\Column(name="static", type="boolean", nullable=true)
     */
    protected $static;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="task_id", type="text", nullable=true)
     */
    protected $taskId;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="ip_addresses_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\Instances
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Instances")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     * })
     */
    protected $instance;
}

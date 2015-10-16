<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersistentDisks
 *
 * @ORM\Table(name="persistent_disks", uniqueConstraints={@ORM\UniqueConstraint(name="persistent_disks_disk_cid_key", columns={"disk_cid"})}, indexes={@ORM\Index(name="IDX_668636903A51721D", columns={"instance_id"})})
 * @ORM\Entity
 */
class PersistentDisks extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="disk_cid", type="text", nullable=false)
     */
    protected $diskCid;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    protected $size;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    protected $active;

    /**
     * @var string
     *
     * @ORM\Column(name="cloud_properties_json", type="text", nullable=true)
     */
    protected $cloudPropertiesJson;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="persistent_disks_id_seq", allocationSize=1, initialValue=1)
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

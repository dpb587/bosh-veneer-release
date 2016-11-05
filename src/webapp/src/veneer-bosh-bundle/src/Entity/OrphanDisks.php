<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrphanDisks
 *
 * @ORM\Table(name="orphan_disks", uniqueConstraints={@ORM\UniqueConstraint(name="orphan_disks_disk_cid_key", columns={"disk_cid"})}, indexes={@ORM\Index(name="orphan_disks_orphaned_at_index", columns={"created_at"})})
 * @ORM\Entity
 */
class OrphanDisks extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @var string
     *
     * @ORM\Column(name="availability_zone", type="text", nullable=true)
     */
    protected $availabilityZone;

    /**
     * @var string
     *
     * @ORM\Column(name="deployment_name", type="text", nullable=false)
     */
    protected $deploymentName;

    /**
     * @var string
     *
     * @ORM\Column(name="instance_name", type="text", nullable=false)
     */
    protected $instanceName;

    /**
     * @var string
     *
     * @ORM\Column(name="cloud_properties_json", type="text", nullable=true)
     */
    protected $cloudPropertiesJson;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="orphan_disks_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

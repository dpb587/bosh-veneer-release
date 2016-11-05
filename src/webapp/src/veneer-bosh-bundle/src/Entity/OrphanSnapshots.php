<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrphanSnapshots
 *
 * @ORM\Table(name="orphan_snapshots", uniqueConstraints={@ORM\UniqueConstraint(name="orphan_snapshots_snapshot_cid_key", columns={"snapshot_cid"})}, indexes={@ORM\Index(name="orphan_snapshots_orphaned_at_index", columns={"created_at"}), @ORM\Index(name="IDX_198762ACA2D26917", columns={"orphan_disk_id"})})
 * @ORM\Entity
 */
class OrphanSnapshots extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="snapshot_cid", type="text", nullable=false)
     */
    protected $snapshotCid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="clean", type="boolean", nullable=true)
     */
    protected $clean;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="snapshot_created_at", type="datetime", nullable=true)
     */
    protected $snapshotCreatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="orphan_snapshots_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\OrphanDisks
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\OrphanDisks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orphan_disk_id", referencedColumnName="id")
     * })
     */
    protected $orphanDisk;


}

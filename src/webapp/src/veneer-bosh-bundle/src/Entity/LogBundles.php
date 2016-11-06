<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogBundles.
 *
 * @ORM\Table(name="log_bundles", uniqueConstraints={@ORM\UniqueConstraint(name="log_bundles_blobstore_id_key", columns={"blobstore_id"})}, indexes={@ORM\Index(name="log_bundles_timestamp_index", columns={"timestamp"})})
 * @ORM\Entity
 */
class LogBundles extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="blobstore_id", type="text", nullable=false)
     */
    protected $blobstoreId;

    /**
     * @var datetime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    protected $timestamp;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="log_bundles_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;
}

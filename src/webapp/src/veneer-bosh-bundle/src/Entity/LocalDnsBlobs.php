<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LocalDnsBlobs
 *
 * @ORM\Table(name="local_dns_blobs")
 * @ORM\Entity
 */
class LocalDnsBlobs extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="blobstore_id", type="text", nullable=false)
     */
    protected $blobstoreId;

    /**
     * @var string
     *
     * @ORM\Column(name="sha1", type="text", nullable=false)
     */
    protected $sha1;

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
     * @ORM\SequenceGenerator(sequenceName="local_dns_blobs_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

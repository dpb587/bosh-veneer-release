<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RenderedTemplatesArchives
 *
 * @ORM\Table(name="rendered_templates_archives", indexes={@ORM\Index(name="rendered_templates_archives_created_at_index", columns={"created_at"}), @ORM\Index(name="IDX_CE4FD63A51721D", columns={"instance_id"})})
 * @ORM\Entity
 */
class RenderedTemplatesArchives extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @var string
     *
     * @ORM\Column(name="content_sha1", type="text", nullable=false)
     */
    protected $contentSha1;

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
     * @ORM\SequenceGenerator(sequenceName="rendered_templates_archives_id_seq", allocationSize=1, initialValue=1)
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

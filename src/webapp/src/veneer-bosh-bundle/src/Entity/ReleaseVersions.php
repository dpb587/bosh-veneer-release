<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleaseVersions
 *
 * @ORM\Table(name="release_versions", indexes={@ORM\Index(name="IDX_42DC9BB3B12A727D", columns={"release_id"})})
 * @ORM\Entity
 */
class ReleaseVersions extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="version", type="text", nullable=false)
     */
    protected $version;

    /**
     * @var string
     *
     * @ORM\Column(name="commit_hash", type="text", nullable=true)
     */
    protected $commitHash;

    /**
     * @var boolean
     *
     * @ORM\Column(name="uncommitted_changes", type="boolean", nullable=true)
     */
    protected $uncommittedChanges;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="release_versions_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\Releases
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Releases")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="release_id", referencedColumnName="id")
     * })
     */
    protected $release;


}

<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Packages.
 *
 * @ORM\Table(name="packages", uniqueConstraints={@ORM\UniqueConstraint(name="packages_release_id_name_version_key", columns={"release_id", "name", "version"})}, indexes={@ORM\Index(name="packages_fingerprint_index", columns={"fingerprint"}), @ORM\Index(name="packages_sha1_index", columns={"sha1"}), @ORM\Index(name="IDX_9BB5C0A7B12A727D", columns={"release_id"})})
 * @ORM\Entity
 */
class Packages extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="text", nullable=false)
     */
    protected $version;

    /**
     * @var string
     *
     * @ORM\Column(name="blobstore_id", type="text", nullable=true)
     */
    protected $blobstoreId;

    /**
     * @var string
     *
     * @ORM\Column(name="sha1", type="text", nullable=true)
     */
    protected $sha1;

    /**
     * @var string
     *
     * @ORM\Column(name="dependency_set_json", type="text", nullable=false)
     */
    protected $dependencySetJson;

    /**
     * @var string
     *
     * @ORM\Column(name="fingerprint", type="text", nullable=true)
     */
    protected $fingerprint;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="packages_id_seq", allocationSize=1, initialValue=1)
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

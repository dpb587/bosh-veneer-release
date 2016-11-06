<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Templates.
 *
 * @ORM\Table(name="templates", uniqueConstraints={@ORM\UniqueConstraint(name="templates_release_id_name_version_key", columns={"release_id", "name", "version"})}, indexes={@ORM\Index(name="templates_fingerprint_index", columns={"fingerprint"}), @ORM\Index(name="templates_sha1_index", columns={"sha1"}), @ORM\Index(name="IDX_6F287D8EB12A727D", columns={"release_id"})})
 * @ORM\Entity
 */
class Templates extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @ORM\Column(name="package_names_json", type="text", nullable=false)
     */
    protected $packageNamesJson;

    /**
     * @var string
     *
     * @ORM\Column(name="logs_json", type="text", nullable=true)
     */
    protected $logsJson;

    /**
     * @var string
     *
     * @ORM\Column(name="fingerprint", type="text", nullable=true)
     */
    protected $fingerprint;

    /**
     * @var string
     *
     * @ORM\Column(name="properties_json", type="text", nullable=true)
     */
    protected $propertiesJson;

    /**
     * @var string
     *
     * @ORM\Column(name="consumes_json", type="text", nullable=true)
     */
    protected $consumesJson;

    /**
     * @var string
     *
     * @ORM\Column(name="provides_json", type="text", nullable=true)
     */
    protected $providesJson;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="templates_id_seq", allocationSize=1, initialValue=1)
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

<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompiledPackages.
 *
 * @ORM\Table(name="compiled_packages", uniqueConstraints={@ORM\UniqueConstraint(name="package_stemcell_build_idx", columns={"package_id", "stemcell_os", "stemcell_version", "build"}), @ORM\UniqueConstraint(name="package_stemcell_dependency_idx", columns={"package_id", "stemcell_os", "stemcell_version", "dependency_key_sha1"})}, indexes={@ORM\Index(name="IDX_4A96D06F44CABFF", columns={"package_id"})})
 * @ORM\Entity
 */
class CompiledPackages extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @ORM\Column(name="dependency_key", type="text", nullable=false)
     */
    protected $dependencyKey;

    /**
     * @var int
     *
     * @ORM\Column(name="build", type="integer", nullable=false)
     */
    protected $build;

    /**
     * @var string
     *
     * @ORM\Column(name="dependency_key_sha1", type="text", nullable=false)
     */
    protected $dependencyKeySha1;

    /**
     * @var string
     *
     * @ORM\Column(name="stemcell_os", type="text", nullable=true)
     */
    protected $stemcellOs;

    /**
     * @var string
     *
     * @ORM\Column(name="stemcell_version", type="text", nullable=true)
     */
    protected $stemcellVersion;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="compiled_packages_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\Packages
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Packages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="package_id", referencedColumnName="id")
     * })
     */
    protected $package;
}

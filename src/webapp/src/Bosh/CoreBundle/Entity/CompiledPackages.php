<?php

namespace Bosh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompiledPackages
 *
 * @ORM\Table(name="compiled_packages", uniqueConstraints={@ORM\UniqueConstraint(name="compiled_packages_package_id_stemcell_id_build_key", columns={"package_id", "stemcell_id", "build"}), @ORM\UniqueConstraint(name="package_stemcell_dependency_key_sha1_idx", columns={"package_id", "stemcell_id", "dependency_key_sha1"})}, indexes={@ORM\Index(name="IDX_4A96D06F44CABFF", columns={"package_id"}), @ORM\Index(name="IDX_4A96D06F8AAD739", columns={"stemcell_id"})})
 * @ORM\Entity
 */
class CompiledPackages extends \Bosh\CoreBundle\Service\AbstractEntity
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
     * @var integer
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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="compiled_packages_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Bosh\CoreBundle\Entity\Stemcells
     *
     * @ORM\ManyToOne(targetEntity="Bosh\CoreBundle\Entity\Stemcells")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="stemcell_id", referencedColumnName="id")
     * })
     */
    protected $stemcell;

    /**
     * @var \Bosh\CoreBundle\Entity\Packages
     *
     * @ORM\ManyToOne(targetEntity="Bosh\CoreBundle\Entity\Packages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="package_id", referencedColumnName="id")
     * })
     */
    protected $package;


}

<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PackagesReleaseVersions
 *
 * @ORM\Table(name="packages_release_versions", uniqueConstraints={@ORM\UniqueConstraint(name="packages_release_versions_package_id_release_version_id_key", columns={"package_id", "release_version_id"})}, indexes={@ORM\Index(name="IDX_367D77D2F44CABFF", columns={"package_id"}), @ORM\Index(name="IDX_367D77D2265B2DBF", columns={"release_version_id"})})
 * @ORM\Entity
 */
class PackagesReleaseVersions extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="packages_release_versions_id_seq", allocationSize=1, initialValue=1)
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

    /**
     * @var \Veneer\BoshBundle\Entity\ReleaseVersions
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\ReleaseVersions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="release_version_id", referencedColumnName="id")
     * })
     */
    protected $releaseVersion;


}

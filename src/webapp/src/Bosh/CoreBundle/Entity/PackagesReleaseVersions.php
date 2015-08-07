<?php

namespace Bosh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PackagesReleaseVersions
 *
 * @ORM\Table(name="packages_release_versions", uniqueConstraints={@ORM\UniqueConstraint(name="packages_release_versions_package_id_release_version_id_key", columns={"package_id", "release_version_id"})}, indexes={@ORM\Index(name="IDX_367D77D2F44CABFF", columns={"package_id"}), @ORM\Index(name="IDX_367D77D2265B2DBF", columns={"release_version_id"})})
 * @ORM\Entity
 */
class PackagesReleaseVersions extends \Bosh\CoreBundle\Service\AbstractEntity
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
     * @var \Bosh\CoreBundle\Entity\ReleaseVersions
     *
     * @ORM\ManyToOne(targetEntity="Bosh\CoreBundle\Entity\ReleaseVersions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="release_version_id", referencedColumnName="id")
     * })
     */
    protected $releaseVersion;

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

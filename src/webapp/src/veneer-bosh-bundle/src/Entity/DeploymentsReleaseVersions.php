<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeploymentsReleaseVersions
 *
 * @ORM\Table(name="deployments_release_versions", uniqueConstraints={@ORM\UniqueConstraint(name="deployments_release_versions_release_version_id_deployment__key", columns={"release_version_id", "deployment_id"})}, indexes={@ORM\Index(name="IDX_AFD791A3265B2DBF", columns={"release_version_id"}), @ORM\Index(name="IDX_AFD791A39DF4CE98", columns={"deployment_id"})})
 * @ORM\Entity
 */
class DeploymentsReleaseVersions extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="deployments_release_versions_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\ReleaseVersions
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\ReleaseVersions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="release_version_id", referencedColumnName="id")
     * })
     */
    protected $releaseVersion;

    /**
     * @var \Veneer\BoshBundle\Entity\Deployments
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Deployments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deployment_id", referencedColumnName="id")
     * })
     */
    protected $deployment;


}

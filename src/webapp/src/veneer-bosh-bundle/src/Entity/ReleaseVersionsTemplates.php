<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReleaseVersionsTemplates
 *
 * @ORM\Table(name="release_versions_templates", uniqueConstraints={@ORM\UniqueConstraint(name="release_versions_templates_release_version_id_template_id_key", columns={"release_version_id", "template_id"})}, indexes={@ORM\Index(name="IDX_8D933749265B2DBF", columns={"release_version_id"}), @ORM\Index(name="IDX_8D9337495DA0FB8", columns={"template_id"})})
 * @ORM\Entity
 */
class ReleaseVersionsTemplates extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="release_versions_templates_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Veneer\BoshBundle\Entity\Templates
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Templates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     * })
     */
    protected $template;


}

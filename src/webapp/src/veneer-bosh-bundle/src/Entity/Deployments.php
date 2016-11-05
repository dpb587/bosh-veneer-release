<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deployments
 *
 * @ORM\Table(name="deployments", uniqueConstraints={@ORM\UniqueConstraint(name="deployments_name_key", columns={"name"})}, indexes={@ORM\Index(name="IDX_373C43D5DD12E1A4", columns={"cloud_config_id"}), @ORM\Index(name="IDX_373C43D51403E80C", columns={"runtime_config_id"})})
 * @ORM\Entity
 */
class Deployments extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @ORM\Column(name="manifest", type="text", nullable=true)
     */
    protected $manifest;

    /**
     * @var string
     *
     * @ORM\Column(name="link_spec_json", type="text", nullable=true)
     */
    protected $linkSpecJson;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="deployments_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\CloudConfigs
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\CloudConfigs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cloud_config_id", referencedColumnName="id")
     * })
     */
    protected $cloudConfig;

    /**
     * @var \Veneer\BoshBundle\Entity\RuntimeConfigs
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\RuntimeConfigs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="runtime_config_id", referencedColumnName="id")
     * })
     */
    protected $runtimeConfig;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Veneer\BoshBundle\Entity\Teams", inversedBy="deployment")
     * @ORM\JoinTable(name="deployments_teams",
     *   joinColumns={
     *     @ORM\JoinColumn(name="deployment_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     *   }
     * )
     */
    protected $team;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->team = new \Doctrine\Common\Collections\ArrayCollection();
    }

}

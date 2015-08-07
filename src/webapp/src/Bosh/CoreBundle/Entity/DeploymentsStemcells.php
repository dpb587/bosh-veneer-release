<?php

namespace Bosh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeploymentsStemcells
 *
 * @ORM\Table(name="deployments_stemcells", uniqueConstraints={@ORM\UniqueConstraint(name="deployments_stemcells_deployment_id_stemcell_id_key", columns={"deployment_id", "stemcell_id"})}, indexes={@ORM\Index(name="IDX_552730EB9DF4CE98", columns={"deployment_id"}), @ORM\Index(name="IDX_552730EBF8AAD739", columns={"stemcell_id"})})
 * @ORM\Entity
 */
class DeploymentsStemcells extends \Bosh\CoreBundle\Service\AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="deployments_stemcells_id_seq", allocationSize=1, initialValue=1)
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
     * @var \Bosh\CoreBundle\Entity\Deployments
     *
     * @ORM\ManyToOne(targetEntity="Bosh\CoreBundle\Entity\Deployments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deployment_id", referencedColumnName="id")
     * })
     */
    protected $deployment;


}

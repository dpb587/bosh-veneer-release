<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstancesTemplates.
 *
 * @ORM\Table(name="instances_templates", uniqueConstraints={@ORM\UniqueConstraint(name="instances_templates_instance_id_template_id_key", columns={"instance_id", "template_id"})}, indexes={@ORM\Index(name="IDX_B4ED17033A51721D", columns={"instance_id"}), @ORM\Index(name="IDX_B4ED17035DA0FB8", columns={"template_id"})})
 * @ORM\Entity
 */
class InstancesTemplates extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="instances_templates_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\Instances
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Instances")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     * })
     */
    protected $instance;

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

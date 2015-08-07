<?php

namespace Bosh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistryInstances
 *
 * @ORM\Table(name="registry_instances", uniqueConstraints={@ORM\UniqueConstraint(name="registry_instances_instance_id_key", columns={"instance_id"})})
 * @ORM\Entity
 */
class RegistryInstances extends \Bosh\CoreBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="instance_id", type="text", nullable=false)
     */
    protected $instanceId;

    /**
     * @var string
     *
     * @ORM\Column(name="settings", type="text", nullable=false)
     */
    protected $settings;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="registry_instances_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

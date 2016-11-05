<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RuntimeConfigs
 *
 * @ORM\Table(name="runtime_configs", indexes={@ORM\Index(name="runtime_configs_created_at_index", columns={"created_at"})})
 * @ORM\Entity
 */
class RuntimeConfigs extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="properties", type="text", nullable=true)
     */
    protected $properties;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="runtime_configs_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

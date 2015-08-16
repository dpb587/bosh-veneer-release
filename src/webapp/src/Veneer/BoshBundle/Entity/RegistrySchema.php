<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistrySchema
 *
 * @ORM\Table(name="registry_schema")
 * @ORM\Entity
 */
class RegistrySchema extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="text")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="registry_schema_filename_seq", allocationSize=1, initialValue=1)
     */
    protected $filename;


}

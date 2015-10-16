<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchemaMigrations
 *
 * @ORM\Table(name="schema_migrations")
 * @ORM\Entity
 */
class SchemaMigrations extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="text")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="schema_migrations_filename_seq", allocationSize=1, initialValue=1)
     */
    protected $filename;


}

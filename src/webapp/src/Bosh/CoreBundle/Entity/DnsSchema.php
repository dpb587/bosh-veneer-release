<?php

namespace Bosh\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DnsSchema
 *
 * @ORM\Table(name="dns_schema")
 * @ORM\Entity
 */
class DnsSchema extends \Bosh\CoreBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="text")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="dns_schema_filename_seq", allocationSize=1, initialValue=1)
     */
    protected $filename;


}

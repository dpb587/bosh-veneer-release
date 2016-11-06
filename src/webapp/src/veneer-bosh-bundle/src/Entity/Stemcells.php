<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stemcells.
 *
 * @ORM\Table(name="stemcells", uniqueConstraints={@ORM\UniqueConstraint(name="stemcells_name_version_key", columns={"name", "version"})})
 * @ORM\Entity
 */
class Stemcells extends \Veneer\BoshBundle\Service\AbstractEntity
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
     * @ORM\Column(name="version", type="text", nullable=false)
     */
    protected $version;

    /**
     * @var string
     *
     * @ORM\Column(name="cid", type="text", nullable=false)
     */
    protected $cid;

    /**
     * @var string
     *
     * @ORM\Column(name="sha1", type="text", nullable=true)
     */
    protected $sha1;

    /**
     * @var string
     *
     * @ORM\Column(name="operating_system", type="text", nullable=true)
     */
    protected $operatingSystem;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="stemcells_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;
}

<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Records
 *
 * @ORM\Table(name="records", indexes={@ORM\Index(name="records_name_index", columns={"name"}), @ORM\Index(name="records_domain_id_index", columns={"domain_id"}), @ORM\Index(name="records_name_type_index", columns={"name", "type"})})
 * @ORM\Entity
 */
class Records extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=true)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=4098, nullable=true)
     */
    protected $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="ttl", type="integer", nullable=true)
     */
    protected $ttl;

    /**
     * @var integer
     *
     * @ORM\Column(name="prio", type="integer", nullable=true)
     */
    protected $prio;

    /**
     * @var integer
     *
     * @ORM\Column(name="change_date", type="integer", nullable=true)
     */
    protected $changeDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="records_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \Veneer\BoshBundle\Entity\Domains
     *
     * @ORM\ManyToOne(targetEntity="Veneer\BoshBundle\Entity\Domains")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="domain_id", referencedColumnName="id")
     * })
     */
    protected $domain;


}

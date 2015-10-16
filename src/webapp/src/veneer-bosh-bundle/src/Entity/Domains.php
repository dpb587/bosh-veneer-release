<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Domains
 *
 * @ORM\Table(name="domains", uniqueConstraints={@ORM\UniqueConstraint(name="domains_name_key", columns={"name"})})
 * @ORM\Entity
 */
class Domains extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="master", type="string", length=128, nullable=true)
     */
    protected $master;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_check", type="integer", nullable=true)
     */
    protected $lastCheck;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=6, nullable=false)
     */
    protected $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="notified_serial", type="integer", nullable=true)
     */
    protected $notifiedSerial;

    /**
     * @var string
     *
     * @ORM\Column(name="account", type="string", length=40, nullable=true)
     */
    protected $account;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="domains_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

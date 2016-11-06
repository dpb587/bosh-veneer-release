<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Locks.
 *
 * @ORM\Table(name="locks", uniqueConstraints={@ORM\UniqueConstraint(name="locks_name_key", columns={"name"}), @ORM\UniqueConstraint(name="locks_uid_key", columns={"uid"})})
 * @ORM\Entity
 */
class Locks extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var datetime
     *
     * @ORM\Column(name="expired_at", type="datetime", nullable=false)
     */
    protected $expiredAt;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="uid", type="text", nullable=false)
     */
    protected $uid;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="locks_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;
}

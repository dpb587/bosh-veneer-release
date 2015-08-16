<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tasks
 *
 * @ORM\Table(name="tasks", indexes={@ORM\Index(name="tasks_state_index", columns={"state"}), @ORM\Index(name="tasks_timestamp_index", columns={"timestamp"}), @ORM\Index(name="tasks_description_index", columns={"description"})})
 * @ORM\Entity
 */
class Tasks extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="state", type="text", nullable=false)
     */
    protected $state;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    protected $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="text", nullable=true)
     */
    protected $result;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="text", nullable=true)
     */
    protected $output;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checkpoint_time", type="datetime", nullable=true)
     */
    protected $checkpointTime;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", nullable=false)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="text", nullable=true)
     */
    protected $username;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tasks_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

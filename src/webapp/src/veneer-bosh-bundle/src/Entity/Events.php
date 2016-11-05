<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Events
 *
 * @ORM\Table(name="events", indexes={@ORM\Index(name="events_timestamp_index", columns={"timestamp"})})
 * @ORM\Entity
 */
class Events extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    protected $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="text", nullable=false)
     */
    protected $user;

    /**
     * @var datetime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    protected $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="text", nullable=false)
     */
    protected $action;

    /**
     * @var string
     *
     * @ORM\Column(name="object_type", type="text", nullable=false)
     */
    protected $objectType;

    /**
     * @var string
     *
     * @ORM\Column(name="object_name", type="text", nullable=true)
     */
    protected $objectName;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="text", nullable=true)
     */
    protected $error;

    /**
     * @var string
     *
     * @ORM\Column(name="task", type="text", nullable=true)
     */
    protected $task;

    /**
     * @var string
     *
     * @ORM\Column(name="deployment", type="text", nullable=true)
     */
    protected $deployment;

    /**
     * @var string
     *
     * @ORM\Column(name="instance", type="text", nullable=true)
     */
    protected $instance;

    /**
     * @var string
     *
     * @ORM\Column(name="context_json", type="text", nullable=true)
     */
    protected $contextJson;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="events_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;


}

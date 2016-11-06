<?php

namespace Veneer\BoshBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DelayedJobs.
 *
 * @ORM\Table(name="delayed_jobs", indexes={@ORM\Index(name="delayed_jobs_priority", columns={"priority", "run_at"})})
 * @ORM\Entity
 */
class DelayedJobs extends \Veneer\BoshBundle\Service\AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false)
     */
    protected $priority;

    /**
     * @var int
     *
     * @ORM\Column(name="attempts", type="integer", nullable=false)
     */
    protected $attempts;

    /**
     * @var string
     *
     * @ORM\Column(name="handler", type="text", nullable=false)
     */
    protected $handler;

    /**
     * @var string
     *
     * @ORM\Column(name="last_error", type="text", nullable=true)
     */
    protected $lastError;

    /**
     * @var datetime
     *
     * @ORM\Column(name="run_at", type="datetime", nullable=true)
     */
    protected $runAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="locked_at", type="datetime", nullable=true)
     */
    protected $lockedAt;

    /**
     * @var datetime
     *
     * @ORM\Column(name="failed_at", type="datetime", nullable=true)
     */
    protected $failedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="locked_by", type="text", nullable=true)
     */
    protected $lockedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="queue", type="text", nullable=true)
     */
    protected $queue;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="delayed_jobs_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;
}

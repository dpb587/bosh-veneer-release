<?php

namespace Veneer\AwsCpiBundle\Controller\Plugin\CoreMetric\Context;

use Veneer\AwsCpiBundle\Controller\Plugin\CoreMetric\Source\CloudWatchSource;
use Veneer\CoreBundle\Plugin\Metric\Context\ContextInterface;
use Veneer\CoreBundle\Plugin\Metric\Context\ContextTrait;
use Aws\CloudWatch\CloudWatchClient;

class PersistentDiskContext implements ContextInterface
{
    use ContextTrait;

    protected static $config = [
        'read_bytes' => [
            'metric' => 'VolumeReadBytes',
            'defaults' => [
                'title' => 'Read Bytes',
                'color' => '#3D6CAD',
            ],
        ],
        'read_ops' => [
            'metric' => 'VolumeReadOps',
            'defaults' => [
                'title' => 'Read Ops',
                'color' => '#3D6CAD',
            ],
        ],
        'read_time' => [
            'metric' => 'VolumeTotalReadTime',
            'defaults' => [
                'title' => 'Total Read Time',
                'color' => '#3D6CAD',
            ],
        ],
        'write_bytes' => [
            'metric' => 'VolumeWriteBytes',
            'defaults' => [
                'title' => 'Written Bytes',
                'color' => '#D67E41',
            ],
        ],
        'write_ops' => [
            'metric' => 'VolumeWriteOps',
            'defaults' => [
                'title' => 'Written Ops',
                'color' => '#D67E41',
            ],
        ],
        'write_time' => [
            'metric' => 'VolumeTotalWriteTime',
            'defaults' => [
                'title' => 'Total Write Time',
                'color' => '#D67E41',
            ],
        ],
        'idle_time' => [
            'metric' => 'VolumeIdleTime',
            'defaults' => [
                'title' => 'Idle Time',
                'color' => '#535055',
            ],
        ],
        'queue_length' => [
            'metric' => 'VolumeQueueLength',
            'defaults' => [
                'title' => 'Queue Length',
                'color' => '#535055',
            ],
        ],
    ];

    protected $cloudwatch;

    public function __construct(CloudWatchClient $cloudwatch)
    {
        $this->cloudwatch = $cloudwatch;
    }

    public function resolve($name)
    {
        if (!isset(self::$config[$name])) {
            throw new \InvalidArgumentException();
        }

        $config = self::$config[$name];

        return new CloudWatchSource(
            $this->cloudwatch,
            'AWS/EBS',
            $config['metric'],
            [
                [
                    'Name' => 'VolumeId',
                    'Value' => $this->context['persistent_disk']['diskCid'],
                ],
            ],
            $config['defaults']
        );
    }
}

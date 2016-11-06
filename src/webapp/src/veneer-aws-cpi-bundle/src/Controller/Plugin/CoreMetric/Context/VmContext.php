<?php

namespace Veneer\AwsCpiBundle\Controller\Plugin\CoreMetric\Context;

use Veneer\AwsCpiBundle\Controller\Plugin\CoreMetric\Source\CloudWatchSource;
use Aws\CloudWatch\CloudWatchClient;

class VmContext implements ContextInterface
{
    use ContextTrait;

    protected static $config = [
        'cpu_utilization' => [
            'metric' => 'CPUUtilization',
            'defaults' => [
                'title' => 'CPU Utilization',
                'color' => '#535055',
            ],
        ],
        'cpu_credit_balance' => [
            'metric' => 'CPUCreditBalance',
            'defaults' => [
                'title' => 'CPU Credit Balance',
                'color' => '#3D6CAD',
            ],
        ],
        'cpu_credit_usage' => [
            'metric' => 'CPUCreditUsage',
            'defaults' => [
                'title' => 'CPU Credit Usage',
                'color' => '#D67E41',
            ],
        ],
        'disk_read_bytes' => [
            'metric' => 'DiskReadBytes',
            'defaults' => [
                'title' => 'Disk Read Bytes',
                'color' => '#3D6CAD',
            ],
        ],
        'disk_read_ops' => [
            'metric' => 'DiskReadOps',
            'defaults' => [
                'title' => 'Disk Read Ops',
                'color' => '#3D6CAD',
            ],
        ],
        'disk_write_bytes' => [
            'metric' => 'DiskWriteBytes',
            'defaults' => [
                'title' => 'Written Bytes',
                'color' => '#D67E41',
            ],
        ],
        'disk_write_ops' => [
            'metric' => 'DiskWriteOps',
            'defaults' => [
                'title' => 'Written Ops',
                'color' => '#D67E41',
            ],
        ],
        'network_in' => [
            'metric' => 'NetworkIn',
            'defaults' => [
                'title' => 'Network In',
                'color' => '#3D6CAD',
            ],
        ],
        'network_out' => [
            'metric' => 'NetworkOut',
            'defaults' => [
                'title' => 'Network Out',
                'color' => '#D67E41',
            ],
        ],
        'status_check_failed' => [
            'metric' => 'StatusCheckFailed',
            'defaults' => [
                'title' => 'Status Check Failed',
                'color' => '#535055',
            ],
        ],
        'status_check_failed_instance' => [
            'metric' => 'StatusCheckFailedInstance',
            'defaults' => [
                'title' => 'Status Check Failed - Instance',
                'color' => '#535055',
            ],
        ],
        'status_check_failed_system' => [
            'metric' => 'StatusCheckFailedSystem',
            'defaults' => [
                'title' => 'Status Check Failed - System',
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
            'AWS/EC2',
            $config['metric'],
            [
                [
                    'Name' => 'InstanceId',
                    'Value' => $this->context['vm']['cid'],
                ],
            ],
            $config['defaults']
        );
    }
}

<?php

namespace Veneer\AwsCpiBundle\Controller\Plugin\CoreMetric\Source;

use Aws\CloudWatch\CloudWatchClient;
use Veneer\CoreBundle\Plugin\Metric\Metric\AbstractMetric;

class CloudWatchSource extends AbstractMetric
{
    static $statisticMap = [
        'avg' => 'Average',
        'max' => 'Maximum',
        'min' => 'Minimum',
        'sum' => 'Sum',
    ];

    protected $client;
    protected $namespace;
    protected $metric;
    protected $dimensions;
    protected $defaults;

    public function __construct(CloudWatchClient $client, $namespace, $metric, array $dimensions, array $defaults = [])
    {
        $this->client = $client;
        $this->namespace = $namespace;
        $this->metric = $metric;
        $this->dimensions = $dimensions;
        $this->defaults = $defaults;
    }

    public function getChartDefaults()
    {
        return $this->defaults;
    }

    public function load(\DateTime $start, \DateTime $end, \DateInterval $interval, $statistic)
    {
        $intervalSeconds = $this->convertIntervalToSeconds($interval);
        $statisticName = self::$statisticMap[$statistic];

        $result = $this->client->getMetricStatistics([
            'Namespace'  => $this->namespace,
            'MetricName' => $this->metric,
            'Dimensions' => $this->dimensions,
            'StartTime'  => $start->format('c'),
            'EndTime'    => $end->format('c'),
            'Period'     => $intervalSeconds,
            'Statistics' => [
                $statisticName,
            ],
        ]);

        $data = [];

        foreach ($result['Datapoints'] as $datapoint) {
            $data[] = [
                (new \DateTime($datapoint['Timestamp']))->format('U') * 1000,
                $datapoint[$statisticName],
            ];
        }

        return $this->normalizeEarly($start, $end, $interval, $data);
    }
}

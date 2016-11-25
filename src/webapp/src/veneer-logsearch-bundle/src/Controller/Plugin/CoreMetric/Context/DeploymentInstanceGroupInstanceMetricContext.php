<?php

namespace Veneer\LogsearchBundle\Controller\Plugin\CoreMetric\Context;

use Veneer\CoreBundle\Plugin\Metric\Context\ContextTrait;
use Veneer\CoreBundle\Plugin\Metric\Context\ContextInterface;
use Veneer\CoreBundle\Plugin\Metric\Metric\AbstractMetric;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\LogsearchBundle\Service\ElasticsearchHelper;

class DeploymentInstanceGroupInstanceMetricContext extends AbstractMetric implements ContextInterface
{
    use ContextTrait;

    protected static $presets = [
        'host.load.load.shortterm' => [
            'title' => 'Short',
            'color' => '#439755',
        ],
        'host.load.load.midterm' => [
            'title' => 'Mid',
            'color' => '#958B43',
        ],
        'host.load.load.longterm' => [
            'title' => 'Long',
            'color' => '#8E262C',
        ],
        'host.memory.memory_used' => [
            'title' => 'Used',
            'color' => '#CA272E',
        ],
        'host.memory.memory_buffered' => [
            'title' => 'Buffered',
            'color' => '#D67E41',
        ],
        'host.memory.memory_cached' => [
            'title' => 'Cached',
            'color' => '#958A48',
        ],
        'host.memory.memory_free' => [
            'title' => 'Free',
            'color' => '#439755',
        ],
    ];

    protected $helper;
    protected $metric;

    public function __construct(ElasticsearchHelper $helper, $metric = null)
    {
        $this->helper = $helper;
        $this->metric = $metric;
        $this->context = new Context();
    }

    public function getChartDefaults()
    {
        return isset(self::$presets[$this->metric]) ? self::$presets[$this->metric] : [];
    }

    public function resolve($name)
    {
        $context = new self($this->helper, ((null !== $this->metric) ? ($this->metric.'.') : '').$name);
        $context->replaceContext($this->context);

        return $context;
    }

    public function load(\DateTime $start, \DateTime $end, \DateInterval $interval, $statistic)
    {
        $intervalSeconds = $this->convertIntervalToSeconds($interval);

        $results = $this->helper->request(
            $start,
            $end,
            'metric/_search?ignore_unavailable=true',
            [
                'aggregations' => [
                    'interval' => [
                        'date_histogram' => [
                            'field' => '@timestamp',
                            'interval' => $intervalSeconds.'s',
                        ],
                        'aggregations' => [
                            'value' => [
                                $statistic => [
                                    'field' => 'value',
                                ],
                            ],
                        ],
                    ],
                ],
                'query' => [
                    'filtered' => [
                        'filter' => [
                            'and' => [
                                $this->helper->generateContextFilters($this->context),
                                $this->helper->generateTimestampFilters($start, $end),
                                [
                                    'term' => [
                                        'name' => $this->metric,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'size' => 0,
            ]
        );

        $data = array_map(
            function (array $v) {
                return [
                    $v['key'],
                    $v['value']['value'],
                ];
            },
            $results['aggregations']['interval']['buckets']
        );

        return $this->normalizeEarly($start, $end, $interval, $data);
    }
}

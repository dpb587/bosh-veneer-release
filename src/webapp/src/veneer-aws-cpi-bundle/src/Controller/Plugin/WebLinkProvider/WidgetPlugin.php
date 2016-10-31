<?php

namespace Veneer\AwsCpiBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class WidgetPlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deployment_instancegroup_index_persistentdisk_summary':
                $metricPrefix = sprintf(
                    'bosh.deployment[%s].job[%s].index[%s].persistent_disk[%s]',
                    $_bosh['deployment']['name'],
                    $_bosh['job']['job'],
                    $_bosh['index']['index'],
                    $_bosh['persistent_disk']['id']
                );

                return [
                    (new Link('cloudwatchbytesstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_core_metric_chart_index',
                            [
                                'title' => 'CloudWatch - Bytes',
                                'series' => [
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.aws_cloudwatch.write_bytes',
                                        'transform' => 'flipY',
                                    ],
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.aws_cloudwatch.read_bytes',
                                    ],
                                ],
                            ]
                        ),
                    (new Link('cloudwatchopsstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_core_metric_chart_index',
                            [
                                'title' => 'CloudWatch - Ops',
                                'series' => [
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.aws_cloudwatch.write_ops',
                                        'transform' => 'flipY',
                                    ],
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.aws_cloudwatch.read_ops',
                                    ],
                                ],
                            ]
                        ),
                    (new Link('cloudwatchqueuestats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_core_metric_chart_index',
                            [
                                'title' => 'CloudWatch - Queue Length',
                                'series' => [
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.aws_cloudwatch.queue_length',
                                    ],
                                ],
                            ]
                        ),
                    (new Link('cloudwatchidlestats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_core_metric_chart_index',
                            [
                                'title' => 'CloudWatch - Queue Length',
                                'series' => [
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.aws_cloudwatch.idle_time',
                                    ],
                                ],
                            ]
                        ),
                ];
            default:
                return [];
        }
    }
}

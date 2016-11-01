<?php

namespace Veneer\LogsearchBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class WidgetPlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deployment_summary':
                return [
                    (new Link('logsearch_monitstatus'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_monitstatus',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_summary':
                $metricPrefix = sprintf(
                    'bosh.deployment[%s].instance_group[%s].instance[%s]',
                    $_bosh['deployment']['name'],
                    $_bosh['instance_group']['job'],
                    $_bosh['instance']['uuid']
                );

                return [
                    (new Link('diskstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_instancegroup_instance_diskstats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ]
                        ),
                    (new Link('loadstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_core_metric_chart_index',
                            [
                                'title' => 'Load Average',
                                'series' => [
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.logsearch_metric.host.load.load.longterm',
                                    ],
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.logsearch_metric.host.load.load.midterm',
                                    ],
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.logsearch_metric.host.load.load.shortterm',
                                    ],
                                ],
                            ]
                        ),
                    (new Link('memstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_core_metric_chart_index',
                            [
                                'title' => 'Memory Usage',
                                'defaults' => [
                                    'stacking' => 'normal',
                                ],
                                'series' => [
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.logsearch_metric.host.memory.memory_used',
                                    ],
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.logsearch_metric.host.memory.memory_buffered',
                                    ],
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.logsearch_metric.host.memory.memory_cached',
                                    ],
                                    [
                                        'statistic' => 'avg',
                                        'metric' => $metricPrefix . '.logsearch_metric.host.memory.memory_free',
                                    ],
                                ],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_persistentdisk_summary':
                return [
                    (new Link('hoststats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_instancegroup_instance_persistentdisk_hoststats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                                'persistent_disk' => $_bosh['persistent_disk']['id'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_network_summary':
                return [
                    (new Link('hoststats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_instancegroup_instance_network_hoststats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                                'network' => $_bosh['network']['name'],
                            ]
                        ),
                ];
            default:
                return [];
        }
    }
}

<?php

namespace Veneer\LogsearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\DeploymentInstanceGroupInstancePersistentDisk
 */
class DeploymentInstanceGroupInstancePersistentDiskController extends AbstractController
{
    public function hoststatsAction(Context $_bosh)
    {
        $es = $this->container->get('veneer_logsearch.elasticsearch_helper');

        $metricPrefix = 'host.disk_xvdf1.disk_octets';

        $di = new \DateInterval('PT5M');
        $dies = '5m';

        $ds = new \DateTime('-6 hours');
        $ds->sub(new \DateInterval('PT'.($ds->format('i') % 5).'M'.$ds->format('s').'S'));

        $de = new \DateTime('now');
        $de->sub(new \DateInterval('PT'.$de->format('s').'S'));

        $contextFilters = $es->generateContextFilters($_bosh);
        $timestampFilters = $es->generateTimestampFilters($ds, $de);

        $results = $es->request(
            $ds,
            $de,
            'metric/_msearch',
            implode(
                '',
                array_map(
                    function ($v) {
                        return '{"ignore_unavailable":true}'."\n".json_encode($v)."\n";
                    },
                    array_map(
                        function ($metric) use ($contextFilters, $timestampFilters, $dies, $metricPrefix) {
                            return [
                                'aggregations' => [
                                    'interval' => [
                                        'date_histogram' => [
                                            'field' => '@timestamp',
                                            'interval' => $dies,
                                        ],
                                        'aggregations' => [
                                            'value' => [
                                                'sum' => [
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
                                                $contextFilters,
                                                $timestampFilters,
                                                [
                                                    'term' => [
                                                        'name' => $metricPrefix.'.'.$metric,
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'size' => 0,
                            ];
                        },
                        [
                            'read',
                            'write',
                        ]
                    )
                )
            )
        );

        $read = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][0]['aggregations']['interval']['buckets']
        );

        $write = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][1]['aggregations']['interval']['buckets']
        );

        return $this->renderApi(
            'VeneerLogsearchBundle:DeploymentInstanceGroupInstancePersistentDisk:hoststats.html.twig',
            [
                'start' => $ds->format('U') * 1000,
                'start_string' => $ds->format('Y-m-d H:i:s'),
                'end' => $de->format('U') * 1000,
                'end_string' => $de->format('Y-m-d H:i:s'),
                'interval' => $dies,
                'series' => [
                    'octets_read' => $read,
                    'octets_write' => $write,
                ],
            ]
        );
    }
}

<?php

namespace Veneer\LogsearchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;

class DeploymentInstanceGroupInstanceNetworkController extends AbstractController
{
    public function hoststatsAction(array $_bosh)
    {
        $es = $this->container->get('veneer_logsearch.elasticsearch_helper');

        $metricPrefix = 'host.interface_eth0';

        $di = new \DateInterval('PT5M');
        $dies = '5m';

        $ds = new \DateTime('-6 hours');
        $ds->sub(new \DateInterval('PT' . ($ds->format('i') % 5) . 'M' . $ds->format('s') . 'S'));

        $de = new \DateTime('now');
        $de->sub(new \DateInterval('PT' . $de->format('s') . 'S'));

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
                        return '{"ignore_unavailable":true}' . "\n" . json_encode($v) . "\n";
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
                                                        'name' => $metricPrefix . '.' . $metric,
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
                            'if_octets.rx',
                            'if_octets.tx',
                        ]
                    )
                )
            )
        );

        $rx = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][0]['aggregations']['interval']['buckets']
        );

        $tx = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][1]['aggregations']['interval']['buckets']
        );

        return $this->renderApi(
            'VeneerLogsearchBundle:DeploymentInstanceGroupInstanceNetwork:hoststats.html.twig',
            [
                'start' => $ds->format('U') * 1000,
                'start_string' => $ds->format('Y-m-d H:i:s'),
                'end' => $de->format('U') * 1000,
                'end_string' => $de->format('Y-m-d H:i:s'),
                'interval' => $dies,
                'series' => [
                    'octets_rx' => $rx,
                    'octets_tx' => $tx,
                ]
            ]
        );
    }
}

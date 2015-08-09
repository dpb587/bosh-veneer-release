<?php

namespace Bosh\LogsearchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentInstanceController extends AbstractController
{
    public function diskstatsAction(array $_context)
    {
        $es = $this->container->get('bosh_logsearch.elasticsearch_helper');

        $di = new \DateInterval('PT5M');
        $dies = '5m';

        $ds = new \DateTime('-6 hours');
        $ds->sub(new \DateInterval('PT' . ($ds->format('i') % 5) . 'M' . $ds->format('s') . 'S'));

        $de = new \DateTime('now');
        $de->sub(new \DateInterval('PT' . $de->format('s') . 'S'));

        $contextFilters = $es->generateContextFilters($_context);
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
                        function ($metric) use ($contextFilters, $timestampFilters, $dies) {
                            return [
                                'aggregations' => [
                                    'interval' => [
                                        'date_histogram' => [
                                            'field' => '@timestamp',
                                            'interval' => $dies,
                                        ],
                                        'aggregations' => [
                                            'value' => [
                                                'avg' => [
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
                                                        'name' => $metric,
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
                            'host.df_xvda1.df_complex_used',
                            'host.df_xvda1.df_complex_free',
                            'host.df_xvdb2.df_complex_used',
                            'host.df_xvdb2.df_complex_free',
                            'host.df_xvdf1.df_complex_used',
                            'host.df_xvdf1.df_complex_free',
                        ]
                    )
                )
            )
        );

        $systemUsed = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][0]['aggregations']['interval']['buckets']
        );
        $systemFree = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][1]['aggregations']['interval']['buckets']
        );

        foreach ($systemFree as $i => $step) {
            $systemUsed[$i]['y'] = ceil($systemUsed[$i]['y'] / ($systemUsed[$i]['y'] + $step['y']) * 100);
        }


        $ephemeralUsed = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][2]['aggregations']['interval']['buckets']
        );
        $ephemeralFree = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][3]['aggregations']['interval']['buckets']
        );

        foreach ($ephemeralFree as $i => $step) {
            $ephemeralUsed[$i]['y'] = ceil($ephemeralUsed[$i]['y'] / ($ephemeralUsed[$i]['y'] + $step['y']) * 100);
        }

        $persistentUsed = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][4]['aggregations']['interval']['buckets']
        );
        $persistentFree = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][5]['aggregations']['interval']['buckets']
        );

        foreach ($persistentFree as $i => $step) {
            $persistentUsed[$i]['y'] = ceil($persistentUsed[$i]['y'] / ($persistentUsed[$i]['y'] + $step['y']) * 100);
        }

        return $this->renderApi(
            'BoshLogsearchBundle:DeploymentInstance:diskstats.html.twig',
            [
                'start' => $ds->format('U') * 1000,
                'start_string' => $ds->format('Y-m-d H:i:s'),
                'end' => $de->format('U') * 1000,
                'end_string' => $de->format('Y-m-d H:i:s'),
                'interval' => $dies,
                'series' => [
                    'system_pct' => $systemUsed,
                    'ephemeral_pct' => $ephemeralUsed,
                    'persistent_pct' => $persistentUsed,
                ]
            ]
        );
    }

    public function loadstatsAction(array $_context)
    {
        $es = $this->container->get('bosh_logsearch.elasticsearch_helper');

        $di = new \DateInterval('PT5M');
        $dies = '5m';

        $ds = new \DateTime('-6 hours');
        $ds->sub(new \DateInterval('PT' . ($ds->format('i') % 5) . 'M' . $ds->format('s') . 'S'));

        $de = new \DateTime('now');
        $de->sub(new \DateInterval('PT' . $de->format('s') . 'S'));

        $contextFilters = $es->generateContextFilters($_context);
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
                        function ($metric) use ($contextFilters, $timestampFilters, $dies) {
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
                                                        'name' => $metric,
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
                            'host.load.load.shortterm',
                            'host.load.load.midterm',
                            'host.load.load.longterm',
                        ]
                    )
                )
            )
        );

        $short = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][0]['aggregations']['interval']['buckets']
        );

        $mid = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][1]['aggregations']['interval']['buckets']
        );

        $long = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][2]['aggregations']['interval']['buckets']
        );

        return $this->renderApi(
            'BoshLogsearchBundle:DeploymentInstance:loadstats.html.twig',
            [
                'start' => $ds->format('U') * 1000,
                'start_string' => $ds->format('Y-m-d H:i:s'),
                'end' => $de->format('U') * 1000,
                'end_string' => $de->format('Y-m-d H:i:s'),
                'interval' => $dies,
                'series' => [
                    'short' => $short,
                    'mid' => $mid,
                    'long' => $long,
                ]
            ]
        );
    }

    public function memstatsAction(array $_context)
    {
        $es = $this->container->get('bosh_logsearch.elasticsearch_helper');

        $di = new \DateInterval('PT5M');
        $dies = '5m';

        $ds = new \DateTime('-6 hours');
        $ds->sub(new \DateInterval('PT' . ($ds->format('i') % 5) . 'M' . $ds->format('s') . 'S'));

        $de = new \DateTime('now');
        $de->sub(new \DateInterval('PT' . $de->format('s') . 'S'));

        $contextFilters = $es->generateContextFilters($_context);
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
                        function ($metric) use ($contextFilters, $timestampFilters, $dies) {
                            return [
                                'aggregations' => [
                                    'interval' => [
                                        'date_histogram' => [
                                            'field' => '@timestamp',
                                            'interval' => $dies,
                                        ],
                                        'aggregations' => [
                                            'value' => [
                                                'avg' => [
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
                                                        'name' => $metric,
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
                            'host.memory.memory_used',
                            'host.memory.memory_buffered',
                            'host.memory.memory_cached',
                            'host.memory.memory_free',
                        ]
                    )
                )
            )
        );

        $used = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][0]['aggregations']['interval']['buckets']
        );

        $buffered = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][1]['aggregations']['interval']['buckets']
        );

        $cached = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][2]['aggregations']['interval']['buckets']
        );

        $free = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][3]['aggregations']['interval']['buckets']
        );

        return $this->renderApi(
            'BoshLogsearchBundle:DeploymentInstance:memstats.html.twig',
            [
                'start' => $ds->format('U') * 1000,
                'start_string' => $ds->format('Y-m-d H:i:s'),
                'end' => $de->format('U') * 1000,
                'end_string' => $de->format('Y-m-d H:i:s'),
                'interval' => $dies,
                'series' => [
                    'used' => $used,
                    'buffered' => $buffered,
                    'cached' => $cached,
                    'free' => $free,
                ]
            ]
        );
    }
}

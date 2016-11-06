<?php

namespace Veneer\LogsearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;

class DeploymentInstanceGroupInstanceController extends AbstractController
{
    public function diskstatsAction(array $_bosh)
    {
        $es = $this->container->get('veneer_logsearch.elasticsearch_helper');

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
                            'host.df_xvdg1.df_complex_used',
                            'host.df_xvdg1.df_complex_free',
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
            if (isset($systemUsed[$i]['y'], $step['y'])) {
                $systemUsed[$i]['y'] = ceil($systemUsed[$i]['y'] / ($systemUsed[$i]['y'] + $step['y']) * 100);
            } else {
                $systemUsed[$i]['y'] = null;
            }
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
            if (isset($ephemeralUsed[$i]['y'], $step['y'])) {
                $ephemeralUsed[$i]['y'] = ceil($ephemeralUsed[$i]['y'] / ($ephemeralUsed[$i]['y'] + $step['y']) * 100);
            } else {
                $ephemeralUsed[$i]['y'] = null;
            }
        }

        $persistentUsedF = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][4]['aggregations']['interval']['buckets']
        );
        $persistentFreeF = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][5]['aggregations']['interval']['buckets']
        );

        $persistentUsedG = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][6]['aggregations']['interval']['buckets']
        );
        $persistentFreeG = $es->reduceDateHistogram(
            $ds,
            $de,
            $di,
            $results['responses'][7]['aggregations']['interval']['buckets']
        );

        foreach ($persistentFreeF as $i => $step) {
            if (isset($persistentUsedF[$i]['y'], $step['y'])) {
                $persistentUsedF[$i]['y'] = ceil($persistentUsedF[$i]['y'] / ($persistentUsedF[$i]['y'] + $step['y']) * 100);
            } elseif (isset($persistentUsedG[$i]['y'], $persistentFreeG[$i]['y'])) {
                $persistentUsedF[$i]['y'] = ceil($persistentUsedG[$i]['y'] / ($persistentUsedG[$i]['y'] + $persistentFreeG[$i]['y']) * 100);
            } else {
                $persistentUsedF[$i]['y'] = null;
            }
        }

        return $this->renderApi(
            'VeneerLogsearchBundle:DeploymentInstanceGroupInstance:diskstats.html.twig',
            [
                'start' => $ds->format('U') * 1000,
                'start_string' => $ds->format('Y-m-d H:i:s'),
                'end' => $de->format('U') * 1000,
                'end_string' => $de->format('Y-m-d H:i:s'),
                'interval' => $dies,
                'series' => [
                    'system_pct' => $systemUsed,
                    'ephemeral_pct' => $ephemeralUsed,
                    'persistent_pct' => $persistentUsedF,
                ],
            ]
        );
    }
}

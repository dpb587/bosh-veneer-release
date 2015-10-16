<?php

namespace Veneer\LogsearchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;

class DeploymentController extends AbstractController
{
    public function monitstatusAction(array $_bosh)
    {
        $es = $this->container->get('veneer_logsearch.elasticsearch_helper');

        $ds = new \DateTime('-12 hours');
        $ds->sub(new \DateInterval('PT' . ($ds->format('i') % 5) . 'M' . $ds->format('s') . 'S'));

        $de = new \DateTime('now');
        $de->sub(new \DateInterval('PT' . $de->format('s') . 'S'));

        $contextFilters = $es->generateContextFilters($_bosh);
        $timestampFilters = $es->generateTimestampFilters($ds, $de);

        $esResults = $es->request(
            $ds,
            $de,
            'metric/_search',
            [
                'aggregations' => [
                    'status' => [
                        'terms' => [
                            'field' => 'name',
                            'size' => 0,
                        ],
                        'aggregations' => [
                            'job' => [
                                'terms' => [
                                    'field' => '@source.bosh_job',
                                    'size' => 0,
                                ],
                                'aggregations' => [
                                    'latest' => [
                                        'top_hits' => [
                                            '_source' => [
                                                '@timestamp',
                                                'value',
                                            ],
                                            'size' => 1,
                                            'sort' => [
                                                '@timestamp' => 'desc',
                                            ],
                                        ],
                                    ],
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
                                    'prefix' => [
                                        'name' => 'monit.',
                                    ],
                                ],
                                [
                                    'regexp' => [
                                        'name' => 'monit\\.([^\\.]+)\\.status',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'size' => 0,
            ]
        );

        $results = [];

        foreach ($esResults['aggregations']['status']['buckets'] as $statusBucket) {
            $statusCounts = [];
            $key = preg_replace('/^monit\.([^\.]+)\.status$/', '$1', $statusBucket['key']);

            foreach ($statusBucket['job']['buckets'] as $jobBucket) {
                if (!isset($statusCounts[$jobBucket['latest']['hits']['hits'][0]['_source']['value']])) {
                    $statusCounts[$jobBucket['latest']['hits']['hits'][0]['_source']['value']] = 0;
                }

                $statusCounts[$jobBucket['latest']['hits']['hits'][0]['_source']['value']] += 1;

                $results[$key]['jobs'][$jobBucket['key']] = [
                    'status' => $jobBucket['latest']['hits']['hits'][0]['_source']['value'],
                    'timestamp' => $jobBucket['latest']['hits']['hits'][0]['_source']['@timestamp'],
                ];
            }

            $results[$key]['healthy'] = count($results[$key]['jobs']) == $statusCounts[0];
        }

        return $this->renderApi(
            'VeneerLogsearchBundle:Deployment:monit-status.html.twig',
            [
                'processes' => $results,
            ]
        );
    }
}

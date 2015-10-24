<?php

namespace Veneer\LogsearchBundle\Service\WellnessCheck;

use Veneer\WellnessBundle\Service\Check\Check;
use Veneer\WellnessBundle\Service\Check\Source\SourceInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Elastica\Client;

class ShipperMetricSource implements SourceInterface
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getConfiguration(NodeDefinition $tree)
    {
        $tree->children()
            ->scalarNode('duration')
                ->info('Duration')
                ->defaultValue('5m')
                ->end()
            ->arrayNode('filter')
                ->info('Elasticsearch filters to apply')
                ->prototype('array')
                    ->end()
                ->end()
            ->arrayNode('query')
                ->info('Elasticsearch query to apply')
                ->prototype('array')
                    ->end()
                ->end()
            ->arrayNode('segment')
                ->info('Aggregation terms')
                ->prototype('array')
                    ->children()
                        ->scalarNode('field')
                            ->isRequired()
                            ->end()
                        ->scalarNode('limit')
                            ->defaultValue(10)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->arrayNode('stats')
                ->info('Which stats to collect')
                ->defaultValue([ 'avg' ])
                ->prototype('scalar')
                    #->enum([ 'min', 'max', 'avg' ])
                    ->end()
                ->end()
            ->scalarNode('metric')
                ->info('Metric name')
                ->isRequired()
                ->end()
            ->scalarNode('metric_match')
                ->info('Metric match type (exact, regexp)')
                ->defaultValue('exact')
                ->end()
            ;
    }

    public function load(Check $check)
    {
        $request = [
            'aggregations' => [],
            'query' => [
                'filtered' => [
                    'filter' => [
                        'and' => [
                            [
                                'range' => [
                                    '@timestamp' => [
                                        'gte' => 'now-' . $check['_source.duration'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'size' => 0,
        ];

        if (isset($check['_source.query'])) {
            $request['query']['filtered']['query'] = $check['_source.query'];
        }

        $aggregation =& $request['aggregations'];

        if (isset($check['_source.segment'])) {
            foreach ($check['_source.segment'] as $segmentName => $segmentAggregation) {
                $aggregation[$segmentName] = [
                    'terms' => [
                        'field' => $segmentAggregation['field'],
                        'size' => $segmentAggregation['limit'],
                    ],
                ];
                $aggregation[$segmentName]['aggregations'] = [];
                $aggregation =& $aggregation[$segmentName]['aggregations'];
            }
        }

        if ('exact' == $check['_source.metric_match']) {
            $request['query']['filtered']['filter']['and'][] = [
                'term' => [
                    'name' => $check['_source.metric'],
                ],
            ];
        } elseif ('regexp' == $check['_source.metric_match']) {
            $request['query']['filtered']['filter']['and'][] = [
                'regexp' => [
                    'name' => $check['_source.metric'],
                ],
            ];

            $aggregation['metric_match'] = [
                'terms' => [
                    'field' => 'name',
                    'size' => 32,
                ],
                'aggregations' => [],
            ];
            $aggregation =& $aggregation['metric_match']['aggregations'];
        }

        foreach ($check['_source.stats'] as $stat) {
            $aggregation[$stat] = [
                $stat => [
                    'field' => 'value',
                ],
            ];
        }

        // this is cheating; reuse Helper
        $now = new \DateTime('now', new \DateTimezone('UTC'));

        $response = $this->client->request(
            'logstash-' . $now->format('Y.m.d') . '/metric/_search',
            'POST',
            $request
        )->getData();

        $yielding = $this->loadRecurse($check, isset($check['_source.segment']) ? array_keys($check['_source.segment']) : [], $response['aggregations']);
        foreach ($yielding as $yield) yield $yield;
    }

    protected function loadRecurse(Check $check, array $segments, array $aggregations, array $extraContext = [], array $source = [])
    {
        if (0 == count($segments)) {
            if ('exact' == $check['_source.metric_match']) {
                foreach ($check['_source.stats'] as $stat) {
                    $source[$stat] = $aggregations[$stat]['value'];
                }
            } elseif ('regexp' == $check['_source.metric_match']) {
                foreach ($aggregations['metric_match']['buckets'] as $bucket) {
                    $key = $bucket['key'];

                    foreach ($check['_source.stats'] as $stat) {
                        $source[$key . '.' . $stat] = $bucket[$stat]['value'];
                    }
                }
            }

            $icheck = clone $check;
            $icheck->mergeContext($extraContext);
            $icheck->setSource($source);

            yield $icheck;

            return;
        }

        $segmentName = array_shift($segments);

        foreach ($aggregations[$segmentName]['buckets'] as $bucket) {
            if ('context.' == substr($segmentName, 0, 8)) {
                $extraContext[substr($segmentName, 8)] = $bucket['key'];
            } else {
                $source[$segmentName] = $bucket['key'];
            }

            $yielding = $this->loadRecurse($check, $segments, $bucket, $extraContext, $source);

            foreach ($yielding as $yield) yield $yield;
        }
    }
}

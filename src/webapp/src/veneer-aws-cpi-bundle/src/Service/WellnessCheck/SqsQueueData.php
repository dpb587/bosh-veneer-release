<?php

namespace Veneer\AwsCpiBundle\Service\WellnessCheck;

use Veneer\WellnessBundle\Service\Check\Check;
use Veneer\WellnessBundle\Service\Check\Data\DataInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class SqsQueueData implements DataInterface
{
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function getConfiguration(NodeDefinition $tree)
    {
        $tree->children()
            ->scalarNode('client')
                ->info('Service name for client')
                ->defaultValue('veneer_awscpi.default_client.sqs')
                ->end()
            ->scalarNode('queue_url')
                ->info('The full Queue URL')
                ->isRequired()
                ->end()
            ;
    }

    public function load(Check $check)
    {
        $queueAttributes = $this->client->getQueueAttributes([
            'QueueUrl' => $check['_lookup.queue_url'],
            'AttributeNames' => [
                'ApproximateNumberOfMessages',
                'ApproximateNumberOfMessagesNotVisible',
            ],
        ])->get('Attributes');

        $result = clone $check;
        $result->setLookup([
            'messages_available' => $queueAttributes['ApproximateNumberOfMessages'],
            'messages_inflight' => $queueAttributes['ApproximateNumberOfMessagesNotVisible'],
        ]);

        yield $result;
    }
}

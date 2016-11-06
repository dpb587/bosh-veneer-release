<?php

namespace Veneer\WellnessBundle\Service\Check\Action;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Veneer\WellnessBundle\Service\Check\Check;

class WebhookAction implements ActionInterface
{
    public function getConfiguration(NodeDefinition $tree)
    {
        $tree->children()
            ->stringNode('scheme')
                ->info('Connection Scheme')
                ->defaultValue('https')
            ->scalarNode('method')
                ->info('HTTP Method')
                ->defaultValue('POST')
                ->end()
            ->stringNode('host')
                ->info('Hostname')
                ->isRequired()
                ->end()
            ->integerNode('port')
                ->info('Port')
                ->defaultValue(443)
                ->end()
            ->stringNode('path')
                ->info('The Reply-To of the message')
                ->isRequired()
                ->end()
            ->arrayNode('query')
                ->info('Query String')
                ->useAttributeAsKey('name')
                ->prototype('string')
                    ->end()
                ->end()
            ->arrayNode('headers')
                ->info('Request Headers')
                ->useAttributeAsKey('name')
                ->prototype('string')
                    ->end()
                ->end()
            ->stringNode('body')
                ->info('Request Body')
                ->isRequired()
                ->end()
            ;
    }

    public function execute(Check $check)
    {
        $guzzle = new Client();
    }
}

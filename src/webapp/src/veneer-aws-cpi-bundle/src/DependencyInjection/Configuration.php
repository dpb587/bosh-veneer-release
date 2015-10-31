<?php

namespace Veneer\AwsCpiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('veneer_aws_cpi');

        $rootNode
            ->children()
                ->scalarNode('region')
                    ->info('Region')
                    ->defaultValue('us-east-1')
                    ->end()
                ->arrayNode('api')
                    ->info('API Configuration')
                    ->normalizeKeys(false)
                    ->children()
                        ->scalarNode('access_key_id')
                            ->info('API Access Key ID')
                            ->end()
                        ->scalarNode('secret_access_key')
                            ->info('API Secret Access Key')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
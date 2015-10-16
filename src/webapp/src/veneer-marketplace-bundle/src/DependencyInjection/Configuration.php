<?php

namespace Veneer\MarketplaceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const MARKETPLACE_TYPE_ALIAS = [
        'aws-s3' => 'Veneer\\MarketplaceBundle\\Service\\Marketplace\\AwsS3Marketplace',
        'bosh-hub' => 'Veneer\\MarketplaceBundle\\Service\\Marketplace\\BoshHubMarketplace',
    ];

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('veneer_marketplace');

        $rootNode
            ->children()
                ->arrayNode('marketplaces')
                    ->info('list of marketplaces')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('type')
                                    ->info('class name or one of: ' . implode(', ', array_keys(Configuration::MARKETPLACE_TYPE_ALIAS)))
                                    ->beforeNormalization()
                                        ->ifTrue(function ($v) {
                                            return array_key_exists($v, self::MARKETPLACE_TYPE_ALIAS);
                                        })
                                        ->then(function ($v) {
                                            $m = self::MARKETPLACE_TYPE_ALIAS;
                                            return $m[$v];
                                        })
                                        ->end()
                                    ->end()
                                ->scalarNode('title')
                                    ->info('title shown in the interface')
                                    ->defaultNull()
                                    ->end()
                                ->arrayNode('options')
                                    ->info('type-specific options for the marketplace')
                                    ->end()
                                ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
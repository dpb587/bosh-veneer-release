<?php

namespace Veneer\HubBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const MARKETPLACE_TYPE_ALIAS = [
        'aws-s3' => 'Veneer\\HubBundle\\Service\\Hub\\AwsS3Hub',
        'bosh-hub' => 'Veneer\\HubBundle\\Service\\Hub\\BoshHubHub',
    ];

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('veneer_hub');

        $rootNode
            ->children()
                ->arrayNode('hubs')
                    ->info('list of hubs')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->canBeUnset()
                        ->performNoDeepMerging()
                        ->children()
                            ->scalarNode('type')
                                ->info('class name or one of: '.implode(', ', array_keys(self::MARKETPLACE_TYPE_ALIAS)))
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
                                ->info('type-specific options for the hub')
                                ->normalizeKeys(false)
                                ->useAttributeAsKey('key')
                                ->prototype('variable')
                                    ->end()
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

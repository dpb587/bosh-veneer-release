<?php

namespace Veneer\MarketplaceBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Definition;

class VeneerMarketplaceExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/dic'));

        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerMarketplacesConfiguration($config['marketplaces'], $container);
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    private function registerMarketplacesConfiguration(array $config, ContainerBuilder $container)
    {
        foreach ($config as $marketplaceName => $marketplaceConfig) {
            $args = [];

            if (isset($marketplaceConfig['title']) || isset($marketplaceConfig['options'])) {
                $args[] = $marketplaceConfig['title'];
            }

            if (isset($marketplaceConfig['options'])) {
                $args[] = $marketplaceConfig['options'];
            }

            $definition = new Definition($marketplaceConfig['type'], $args);

            $definition->addTag('veneer_marketplace.marketplaces', [ 'alias' => $marketplaceName ]);

            $container->setDefinition('veneer_marketplace.marketplace.' . $marketplaceName, $definition);
        }
    }
}
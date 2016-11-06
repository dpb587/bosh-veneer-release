<?php

namespace Veneer\HubBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Definition;

class VeneerHubExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/dic'));

        $loader->load('web-request-context.xml');
        $loader->load('web-link-provider.xml');
        $loader->load('services.xml');

        $config = $this->processConfiguration(
            $this->getConfiguration($configs, $container),
            $configs
        );

        $this->registerHubsConfiguration($config['hubs'], $container);
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    private function registerHubsConfiguration(array $config, ContainerBuilder $container)
    {
        foreach ($config as $hubName => $hubConfig) {
            $args = [];

            if (isset($hubConfig['title']) || isset($hubConfig['options'])) {
                $args[] = $hubConfig['title'];
            }

            if (isset($hubConfig['options'])) {
                $args[] = $hubConfig['options'];
            }

            $definition = new Definition($hubConfig['type'], $args);

            $definition->addTag('veneer_hub.hubs', ['alias' => $hubName]);

            $container->setDefinition('veneer_hub.hub.'.$hubName, $definition);
        }
    }
}

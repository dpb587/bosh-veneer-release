<?php

namespace Veneer\WardenCpiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class VeneerWardenCpiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/dic'));

//        $loader->load('form-api.xml');
        $loader->load('form-cpi.xml');
        $loader->load('services.xml');
//        $loader->load('twig.xml');
//        $loader->load('web-link-provider.xml');
//        $loader->load('plugin-core-workspace-environment.xml');
//        $loader->load('core-metric.xml');
//        $loader->load('web-workspace.xml');
    }
}

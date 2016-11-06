<?php

namespace Veneer\LogsearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class VeneerLogsearchExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/dic'));

        $loader->load('core-metric.xml');
        $loader->load('services.xml');
        $loader->load('web-link-provider.xml');
        $loader->load('wellness-check.xml');
    }
}

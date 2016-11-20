<?php

namespace Veneer\BoshEditorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class VeneerBoshEditorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/dic'));

        $loader->load('form.xml');
        $loader->load('schema-map.xml');
        $loader->load('services.xml');
        $loader->load('web-workspace.xml');
        $loader->load('workspace-git.xml');
    }
}

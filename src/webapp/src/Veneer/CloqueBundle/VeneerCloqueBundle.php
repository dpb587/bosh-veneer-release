<?php

namespace Veneer\CloqueBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Veneer\BoshBundle\DependencyInjection\CompilerPass\PluginCompilerPass;
use Veneer\WebBundle\Plugin\Bundle\BundleInterface;

class VeneerCloqueBundle extends Bundle implements BundleInterface
{
    public function getVeneerName()
    {
        return 'cloque';
    }

    public function getVeneerTitle()
    {
        return 'Cloque';
    }

    public function getVeneerDescription()
    {
        return 'Integrate references about your deployment configuration to your repository and GitHub.';
    }

    public function getVeneerRoute()
    {
        return null;
    }
}

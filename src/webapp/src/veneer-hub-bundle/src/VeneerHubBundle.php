<?php

namespace Veneer\HubBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\CoreBundle\Plugin\Bundle\BundleInterface;
use Veneer\CoreBundle\DependencyInjection\CompilerPass\ContainerMapCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VeneerHubBundle extends Bundle implements BundleInterface
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new ContainerMapCompilerPass('veneer_hub.hubs'));
    }

    public function getVeneerName()
    {
        return 'hub';
    }

    public function getVeneerTitle()
    {
        return 'Hub';
    }

    public function getVeneerDescription()
    {
        return 'Utilize releases and stemcells from external services.';
    }

    public function getVeneerRoute()
    {
        return 'veneer_hub_summary';
    }
}

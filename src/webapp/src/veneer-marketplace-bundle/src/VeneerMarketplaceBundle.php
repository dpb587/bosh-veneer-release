<?php

namespace Veneer\MarketplaceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\WebBundle\Plugin\Bundle\BundleInterface;
use Veneer\Component\DependencyInjection\ContainerMapCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VeneerMarketplaceBundle extends Bundle implements BundleInterface
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new ContainerMapCompilerPass('veneer_marketplace.marketplaces'));
    }

    public function getVeneerName()
    {
        return 'marketplace';
    }

    public function getVeneerTitle()
    {
        return 'Marketplace';
    }

    public function getVeneerDescription()
    {
        return 'Utilize releases and stemcells from external services.';
    }

    public function getVeneerRoute()
    {
        return 'veneer_marketplace_summary';
    }
}

<?php

namespace Veneer\WellnessBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\CoreBundle\DependencyInjection\CompilerPass\ContainerMapCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Veneer\CoreBundle\Plugin\Bundle\BundleInterface;

class VeneerWellnessBundle extends Bundle implements BundleInterface
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new ContainerMapCompilerPass('veneer_wellness.check.action'));
        $builder->addCompilerPass(new ContainerMapCompilerPass('veneer_wellness.check.source'));
        $builder->addCompilerPass(new ContainerMapCompilerPass('veneer_wellness.check.condition'));
    }

    public function getVeneerName()
    {
        return 'wellness';
    }

    public function getVeneerTitle()
    {
        return 'Wellness';
    }

    public function getVeneerDescription()
    {
        return 'Monitor and respond to metrics in the cluster.';
    }

    public function getVeneerRoute()
    {
        return 'veneer_wellness_summary';
    }
}

<?php

namespace Veneer\BoshBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Veneer\BoshBundle\DependencyInjection\Security\Factory\BoshDirectorFactory;
use Veneer\CoreBundle\Plugin\Bundle\BundleInterface;

class VeneerBoshBundle extends Bundle implements BundleInterface
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $extension = $builder->getExtension('security');
        $extension->addSecurityListenerFactory(new BoshDirectorFactory());
    }
    public function getVeneerName()
    {
        return 'bosh';
    }


    public function getVeneerTitle()
    {
        return 'BOSH';
    }

    public function getVeneerDescription()
    {
        return 'Browse the current state of your BOSH resources.';
    }

    public function getVeneerRoute()
    {
        return 'veneer_bosh_summary';
    }
}

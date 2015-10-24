<?php

namespace Veneer\OpsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\CoreBundle\Plugin\Bundle\BundleInterface;

class VeneerOpsBundle extends Bundle implements BundleInterface
{
    public function getVeneerName()
    {
        return 'ops';
    }

    public function getVeneerTitle()
    {
        return 'Operations';
    }

    public function getVeneerDescription()
    {
        return 'Configure and deploy resources through the web interface.';
    }

    public function getVeneerRoute()
    {
        return 'veneer_ops_summary';
    }
}

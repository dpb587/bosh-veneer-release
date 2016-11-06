<?php

namespace Veneer\SheafBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\CoreBundle\Plugin\Bundle\BundleInterface;

class VeneerSheafBundle extends Bundle implements BundleInterface
{
    public function getVeneerName()
    {
        return 'sheaf';
    }

    public function getVeneerTitle()
    {
        return 'Sheaf';
    }

    public function getVeneerDescription()
    {
        return 'Preset installations and guided configuration.';
    }

    public function getVeneerRoute()
    {
        return 'veneer_sheaf_summary';
    }
}

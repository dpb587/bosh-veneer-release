<?php

namespace Veneer\WardenCpiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\CoreBundle\Plugin\Bundle\BundleInterface;

class VeneerWardenCpiBundle extends Bundle implements BundleInterface
{
    public function getVeneerName()
    {
        return 'warden';
    }

    public function getVeneerTitle()
    {
        return 'Warden';
    }

    public function getVeneerDescription()
    {
        return 'Integrate Warden as the CPI (for bosh-lite).';
    }

    public function getVeneerRoute()
    {
        return 'veneer_wardencpi_summary';
    }
}

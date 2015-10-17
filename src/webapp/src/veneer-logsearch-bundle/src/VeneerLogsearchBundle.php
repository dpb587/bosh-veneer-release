<?php

namespace Veneer\LogsearchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\CoreBundle\Plugin\Bundle\BundleInterface;

class VeneerLogsearchBundle extends Bundle implements BundleInterface
{
    public function getVeneerName()
    {
        return 'logsearch';
    }

    public function getVeneerTitle()
    {
        return 'Logsearch';
    }

    public function getVeneerDescription()
    {
        return 'Integrate deployment logs and metrics from logsearch-shipper.';
    }

    public function getVeneerRoute()
    {
        return null;
    }
}

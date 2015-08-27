<?php

namespace Veneer\AwsCpiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\WebBundle\Plugin\Bundle\BundleInterface;

class VeneerAwsCpiBundle extends Bundle implements BundleInterface
{
    public function getVeneerName()
    {
        return 'aws-cpi';
    }

    public function getVeneerTitle()
    {
        return 'Amazon Web Services';
    }

    public function getVeneerDescription()
    {
        return 'Integrate AWS as the CPI with external links and integrated CloudWatch metrics.';
    }

    public function getVeneerRoute()
    {
        return null;
    }
}

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

    public function getDeploymentResourcePoolFormType()
    {
        return 'veneer_wardencpi_ops_deployment_resourcepool';
    }

    public function getDeploymentDiskPoolFormType()
    {
        return 'veneer_wardencpi_ops_deployment_diskpool';
    }

    public function getDeploymentNetworkDynamicFormType()
    {
        return 'veneer_wardencpi_ops_deployment_network';
    }

    public function getDeploymentNetworkManualForm()
    {
        return 'veneer_wardencpi_ops_deployment_network';
    }
}

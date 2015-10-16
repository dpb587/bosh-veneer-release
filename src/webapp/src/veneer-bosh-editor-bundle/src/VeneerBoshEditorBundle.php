<?php

namespace Veneer\BoshEditorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\WebBundle\Plugin\Bundle\BundleInterface;

class VeneerBoshEditorBundle extends Bundle implements BundleInterface
{
    public function getVeneerName()
    {
        return 'bosh-editor';
    }

    public function getVeneerTitle()
    {
        return 'BOSH Editor';
    }

    public function getVeneerDescription()
    {
        return 'Configure and deploy resources through the web interface.';
    }

    public function getVeneerRoute()
    {
        return null;
    }
}

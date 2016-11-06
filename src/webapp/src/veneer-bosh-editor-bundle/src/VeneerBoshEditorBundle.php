<?php

namespace Veneer\BoshEditorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\CoreBundle\Plugin\Bundle\BundleInterface;

class VeneerBoshEditorBundle extends Bundle implements BundleInterface
{
    public function getVeneerName()
    {
        return 'editor';
    }

    public function getVeneerTitle()
    {
        return 'BOSH Editor';
    }

    public function getVeneerDescription()
    {
        return 'Edit BOSH resources through the web interface.';
    }

    public function getVeneerRoute()
    {
        return null;
    }
}

<?php

namespace Veneer\CoreBundle\Plugin\Bundle;

interface BundleInterface
{
    public function getVeneerName();
    public function getVeneerTitle();
    public function getVeneerDescription();
    public function getVeneerRoute();
}

<?php

namespace Veneer\WebBundle\Plugin\Bundle;

interface BundleInterface
{
    public function getVeneerName();
    public function getVeneerTitle();
    public function getVeneerDescription();
    public function getVeneerRoute();
}

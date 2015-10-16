<?php

namespace Veneer\MarketplaceBundle\Service\Marketplace;

interface MarketplaceInterface
{
    public function getTitle();
    public function getDescription();

    public function yieldReleases();
    public function yieldStemcells();
}

<?php

namespace Veneer\MarketplaceBundle\Service\Marketplace;

interface MarketplaceInterface
{
    public function getTitle();
    public function getDetails();

    public function authenticateReleaseTarballUrl($tarballUrl);
    public function yieldReleases();

    public function authenticateStemcellTarballUrl($tarballUrl);
    public function yieldStemcells();
}

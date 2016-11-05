<?php

namespace Veneer\HubBundle\Service\Hub;

interface HubInterface
{
    public function getTitle();
    public function getDetails();

    public function authenticateReleaseTarballUrl($tarballUrl);
    public function yieldReleases();

    public function authenticateStemcellTarballUrl($tarballUrl);
    public function yieldStemcells();

    public function authenticateSheafTarballUrl($tarballUrl);
    public function yieldSheaves();
}

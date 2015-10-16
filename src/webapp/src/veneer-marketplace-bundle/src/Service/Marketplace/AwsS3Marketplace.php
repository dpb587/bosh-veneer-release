<?php

namespace Veneer\MarketplaceBundle\Service\Marketplace;

use Veneer\MarketplaceBundle\Entity\ReleaseVersion;
use Veneer\MarketplaceBundle\Entity\StemcellVersion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class AwsS3Marketplace implements MarketplaceInterface
{
    protected $title;
    protected $options;

    protected $client;

    public function __construct($title = null, array $options = [])
    {
        $this->title = (null === $title) ? 'aws-s3' : $title;
        $this->options = array_merge(
            [
                'region' => 'us-east-1',
                'bucket' => null,
                'access_key_id' => null,
                'secret_access_key' => null,
                'release_prefix' => 'release/',
                'release_regex' => '#/(?P<name>[^/]+)\-(?P<version>\d+)\.tgz$#',
                'stemcell_prefix' => 'stemcell/',
                'stemcell_regex' => '#/(?P<name>[^/]+)\-(?P<version>\d+)\.tgz$#',
            ],
            $options
        );
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function yieldReleases()
    {
        // @todo
    }

    public function authenticateReleaseTarballUrl($tarballUrl)
    {
        // @todo
    }

    public function yieldStemcells()
    {
        // @todo
    }

    public function authenticateStemcellTarballUrl($tarballUrl)
    {
        // @todo
    }
}

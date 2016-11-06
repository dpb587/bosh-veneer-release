<?php

namespace Veneer\HubBundle\Service\Hub;

use Symfony\Component\Finder\Finder;
use Veneer\HubBundle\Entity\ReleaseVersion;
use Veneer\HubBundle\Entity\StemcellVersion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Aws\S3\S3Client;

class LocalValiseHub implements HubInterface
{
    protected $title;
    protected $options;

    private $client;

    public function __construct($title = null, array $options = [])
    {
        $this->title = (null === $title) ? 'local-valise' : $title;
        $this->options = $options;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDetails()
    {
        return [
            'Path' => $this->options['path'],
        ];
    }

    public function yieldReleases()
    {
        foreach ((new Finder())->in($this->options['path'] . '/release')->name('*.json') as $path) {
            $json = json_decode(file_get_contents($path), true);

            foreach ($json['versions'] as $releaseVersion) {
                $entity = new ReleaseVersion();
                $entity->setRelease($releaseVersion['name']);
                $entity->setVersion($releaseVersion['version']);
                $entity->setTarballUrl($releaseVersion['url']);
                $entity->setTarballSize($releaseVersion['checksum']['size']);
                $entity->setTarballChecksum('sha1:' . $releaseVersion['checksum']['sha1']);

                yield $entity;
            }
        }
    }

    public function authenticateReleaseTarballUrl($tarballUrl)
    {
        return $tarballUrl;
    }

    public function yieldStemcells()
    {
        foreach ((new Finder())->in($this->options['path'] . '/stemcell')->name('*.json') as $path) {
            $json = json_decode(file_get_contents($path), true);

            foreach ($json['versions'] as $stemcellVersion) {
                $entity = new StemcellVersion();
                $entity->setStemcell($stemcellVersion['name']);
                $entity->setVersion($stemcellVersion['version']);
                $entity->setTarballUrl($stemcellVersion['url']);
                $entity->setTarballSize($stemcellVersion['checksum']['size']);
                $entity->setTarballChecksum('sha1:' . $stemcellVersion['checksum']['sha1']);

                yield $entity;
            }
        }
    }

    public function authenticateStemcellTarballUrl($tarballUrl)
    {
        return $tarballUrl;
    }
}

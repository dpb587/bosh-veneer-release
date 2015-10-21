<?php

namespace Veneer\MarketplaceBundle\Service\Marketplace;

use Veneer\MarketplaceBundle\Entity\ReleaseVersion;
use Veneer\MarketplaceBundle\Entity\StemcellVersion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class BoshHubMarketplace implements MarketplaceInterface
{
    protected $title;
    protected $options;

    protected $client;

    public function __construct($title = null, array $options = [])
    {
        $this->title = (null === $title) ? 'bosh.io' : $title;
        $this->options = array_merge(
            [
                'base_uri' => 'https://bosh.io',
            ],
            $options
        );
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDetails()
    {
        return [
            'Website' => $this->options['base_uri'],
        ];
    }

    public function yieldReleases()
    {
        $baseUri = new Uri($this->options['base_uri']);
        $response = $this->request('releases');

        $dom = new \DOMDocument();
        @$dom->loadHTML($response->getBody());

        $xpath = new \DOMXPath($dom);

        $releases = [];

        foreach ($xpath->query('//li[@class = "list-group-item"]/a[1]') as $release) {
            $href = $release->attributes->getNamedItem('href')->nodeValue;
            $name = trim($release->textContent);

            $releases[$name] = $href;
        }

        foreach ($releases as $releasePage) {
            $response = $this->request($releasePage);

            $dom = new \DOMDocument();
            @$dom->loadHTML($response->getBody());

            $xpath = new \DOMXPath($dom);

            $releaseName = trim($xpath->query('//h3[@class = "page-header"]/span')->item(0)->textContent, '\'');

            foreach ($xpath->query('//li[@class = "list-group-item"]/p[1]') as $versionNode) {
                $anchorNodes = $xpath->query('./a', $versionNode);

                $entity = new ReleaseVersion();
                $entity->setRelease($releaseName);
                $entity->setVersion($anchorNodes->item(0)->textContent);
                $entity->setDetailUrl((string) Uri::resolve($baseUri, $anchorNodes->item(0)->attributes->getNamedItem('href')->nodeValue));
                $entity->setTarballUrl((string) Uri::resolve($baseUri, $anchorNodes->item(1)->attributes->getNamedItem('href')->nodeValue));
                $entity->setTarballChecksum(preg_replace('/\s+/', '', $xpath->query('./span', $versionNode)->item(0)->textContent));

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
        $baseUri = new Uri($this->options['base_uri']);
        $response = $this->request('stemcells');

        $dom = new \DOMDocument();
        @$dom->loadHTML($response->getBody());

        $xpath = new \DOMXPath($dom);

        $stemcells = [];

        foreach ($xpath->query('//li[@class = "list-group-item stemcell"]/div/a[@class = "note"]') as $release) {
            $href = $release->attributes->getNamedItem('href')->nodeValue;
            $name = basename($href);

            $stemcells[$name] = $href;
        }

        foreach ($stemcells as $stemcellName => $stemcellPage) {
            $response = $this->request('api/v1/stemcells/' . $stemcellName);

            $versions = json_decode($response->getBody(), true);

            foreach ($versions as $version) {
                $entity = new StemcellVersion();
                $entity->setStemcell($version['name']);
                $entity->setVersion($version['version']);

                if (isset($version['regular'])) {
                    $entity->setSourceType('regular');
                } elseif (isset($version['light'])) {
                    $entity->setSourceType('light');
                }

                $entity->setDetailUrl((string) Uri::resolve($baseUri, $stemcellPage));
                $entity->setTarballUrl($version[$entity->getSourceType()]['url']);
                $entity->setTarballSize($version[$entity->getSourceType()]['size']);
                $entity->setTarballChecksum('md5:' . $version[$entity->getSourceType()]['md5']);

                yield $entity;
            }
        }
    }

    public function authenticateStemcellTarballUrl($tarballUrl)
    {
        return $tarballUrl;
    }

    protected function request($path)
    {
        if (null === $this->client) {
            $this->client = new Client($this->options);
        }

        return $this->client->get($path);
    }
}

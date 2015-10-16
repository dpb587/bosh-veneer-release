<?php

namespace Veneer\MarketplaceBundle\Service\Marketplace;

use Veneer\MarketplaceBundle\Entity\ReleaseVersion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class BoshHubMarketplace implements MarketplaceInterface
{
    protected $title;
    protected $description;
    protected $clientOptions;

    protected $client;

    public function __construct($server, $title, $description, array $clientOptions = [])
    {
        $this->title = $title;
        $this->description = $description;
        $this->clientOptions = array_merge(
            [
                'base_uri' => $server,
            ],
            $clientOptions
        );
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function yieldReleases()
    {
        $baseUri = new Uri($this->clientOptions['base_uri']);
        $response = $this->getClient()->get('releases');

        $dom = new \DOMDocument();
        @$dom->loadHTML($response->getBody());

        $xpath = new \DOMXPath($dom);

        $releases = [];

        foreach ($xpath->query('//li[@class = "list-group-item"]/a') as $release) {
            $href = $release->attributes->getNamedItem('href')->nodeValue;
            $name = trim($release->textContent);

            $releases[$name] = $href;
        }

        foreach ($releases as $releasePage) {
            $response = $this->getClient()->get($releasePage);

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

                yield $entity;
            }
        }
    }

    public function yieldStemcells()
    {

    }

    protected function getClient()
    {
        if (null === $this->client) {
            $this->client = new Client($this->clientOptions);
        }

        return $this->client;
    }
}

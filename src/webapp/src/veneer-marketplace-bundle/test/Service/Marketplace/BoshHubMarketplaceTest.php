<?php

namespace Veneer\MarketplaceBundle\Test\Service\Marketplace;

use Veneer\MarketplaceBundle\Service\Marketplace\BoshHubMarketplace;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class BoshHubMarketplaceTest extends \PHPUnit_Framework_TestCase
{
    public function testReleases()
    {
        $mockClient = new Client([
            'handler' => HandlerStack::create(new MockHandler([
                new Response(200, [], file_get_contents(__DIR__ . '/_data/BoshHub/releases-index.html')),
                new Response(200, [], file_get_contents(__DIR__ . '/_data/BoshHub/releases-logsearch.html')),
                new Response(200, [], file_get_contents(__DIR__ . '/_data/BoshHub/releases-logsearch-shipper.html')),
            ])),
        ]);

        $stub = $this->getMockBuilder(BoshHubMarketplace::class)
            ->setConstructorArgs([
                'https://bosh.io',
                'bosh.io',
                null
            ])
            ->setMethods([ 'getClient' ])
            ->getMock();
        $stub->method('getClient')->will($this->returnValue($mockClient));

        $yields = iterator_to_array($stub->yieldReleases());

        $this->assertEquals('logsearch', $yields[0]->getRelease());
        $this->assertEquals('23.0.0', $yields[0]->getVersion());
        $this->assertEquals('https://bosh.io/releases/github.com/logsearch/logsearch-boshrelease?version=23.0.0', $yields[0]->getDetailUrl());
        $this->assertEquals('https://bosh.io/d/github.com/logsearch/logsearch-boshrelease?v=23.0.0', $yields[0]->getTarballUrl());

        $this->assertEquals('logsearch-shipper', $yields[12]->getRelease());
        $this->assertEquals('1', $yields[12]->getVersion());
        $this->assertEquals('https://bosh.io/releases/github.com/logsearch/logsearch-shipper-boshrelease?version=1', $yields[12]->getDetailUrl());
        $this->assertEquals('https://bosh.io/d/github.com/logsearch/logsearch-shipper-boshrelease?v=1', $yields[12]->getTarballUrl());

        $this->assertCount(13, $yields);
    }
}

<?php

namespace Veneer\HubBundle\Tests\Service\Hub;

use Veneer\HubBundle\Service\Hub\BoshHubHub;
use GuzzleHttp\Psr7\Response;
use Veneer\HubBundle\Entity\ReleaseVersion;
use Veneer\HubBundle\Entity\StemcellVersion;

class BoshHubMarketplaceTest extends \PHPUnit_Framework_TestCase
{
    public function testReleases()
    {
        $stub = $this->getMockBuilder(BoshHubHub::class)
            ->setMethods(['request'])
            ->getMock();

        $stub->method('request')
            ->withConsecutive(
                [$this->equalTo('releases')],
                [$this->equalTo('/releases/github.com/logsearch/logsearch-boshrelease')],
                [$this->equalTo('/releases/github.com/logsearch/logsearch-shipper-boshrelease')]
            )
            ->will($this->onConsecutiveCalls(
                new Response(200, [], file_get_contents(__DIR__.'/_data/BoshHub/releases-index.html')),
                new Response(200, [], file_get_contents(__DIR__.'/_data/BoshHub/releases-logsearch.html')),
                new Response(200, [], file_get_contents(__DIR__.'/_data/BoshHub/releases-logsearch-shipper.html'))
            ))
            ;

        $yields = iterator_to_array($stub->yieldReleases());

        $this->assertInstanceOf(ReleaseVersion::class, $yields[0]);
        $this->assertEquals('logsearch', $yields[0]->getRelease());
        $this->assertEquals('23.0.0', $yields[0]->getVersion());
        $this->assertEquals('https://bosh.io/releases/github.com/logsearch/logsearch-boshrelease?version=23.0.0', $yields[0]->getDetailUrl());
        $this->assertEquals('https://bosh.io/d/github.com/logsearch/logsearch-boshrelease?v=23.0.0', $yields[0]->getTarballUrl());
        $this->assertEquals('sha1:5afa76e09f41c28565d0ec96a69bd987103e7e7d', $yields[0]->getTarballChecksum());
        $this->assertNull($yields[0]->getTarballSize());

        $this->assertInstanceOf(ReleaseVersion::class, $yields[12]);
        $this->assertEquals('logsearch-shipper', $yields[12]->getRelease());
        $this->assertEquals('1', $yields[12]->getVersion());
        $this->assertEquals('https://bosh.io/releases/github.com/logsearch/logsearch-shipper-boshrelease?version=1', $yields[12]->getDetailUrl());
        $this->assertEquals('https://bosh.io/d/github.com/logsearch/logsearch-shipper-boshrelease?v=1', $yields[12]->getTarballUrl());
        $this->assertEquals('sha1:fa1a2243a76ef526e447e77b493b776fa49a54c2', $yields[12]->getTarballChecksum());
        $this->assertNull($yields[12]->getTarballSize());

        $this->assertCount(13, $yields);
    }

    public function testStemcells()
    {
        $stub = $this->getMockBuilder(BoshHubHub::class)
            ->setMethods(['request'])
            ->getMock();

        $stub->method('request')
            ->withConsecutive(
                [$this->equalTo('stemcells')],
                [$this->equalTo('api/v1/stemcells/bosh-aws-xen-hvm-ubuntu-trusty-go_agent')],
                [$this->equalTo('api/v1/stemcells/bosh-openstack-kvm-centos-7-go_agent')]
            )
            ->will($this->onConsecutiveCalls(
                new Response(200, [], file_get_contents(__DIR__.'/_data/BoshHub/stemcells-index.html')),
                new Response(200, [], file_get_contents(__DIR__.'/_data/BoshHub/stemcells-ubuntu.json')),
                new Response(200, [], file_get_contents(__DIR__.'/_data/BoshHub/stemcells-centos.json'))
            ))
            ;

        $yields = iterator_to_array($stub->yieldStemcells());

        $this->assertInstanceOf(StemcellVersion::class, $yields[0]);
        $this->assertEquals('bosh-aws-xen-hvm-ubuntu-trusty-go_agent', $yields[0]->getStemcell());
        $this->assertEquals('3104', $yields[0]->getVersion());
        $this->assertEquals('light', $yields[12]->getSourceType());
        $this->assertEquals('https://bosh.io/stemcells/bosh-aws-xen-hvm-ubuntu-trusty-go_agent', $yields[0]->getDetailUrl());
        $this->assertEquals('https://d26ekeud912fhb.cloudfront.net/bosh-stemcell/aws/light-bosh-stemcell-3104-aws-xen-hvm-ubuntu-trusty-go_agent.tgz', $yields[0]->getTarballUrl());
        $this->assertEquals('md5:3c28917b5241125d7d2d68703ec64635', $yields[0]->getTarballChecksum());
        $this->assertEquals(18039, $yields[0]->getTarballSize());

        $this->assertInstanceOf(StemcellVersion::class, $yields[59]);
        $this->assertEquals('bosh-openstack-kvm-centos-7-go_agent', $yields[59]->getStemcell());
        $this->assertEquals('3016', $yields[59]->getVersion());
        $this->assertEquals('regular', $yields[59]->getSourceType());
        $this->assertEquals('https://bosh.io/stemcells/bosh-openstack-kvm-centos-7-go_agent', $yields[59]->getDetailUrl());
        $this->assertEquals('https://d26ekeud912fhb.cloudfront.net/bosh-stemcell/openstack/bosh-stemcell-3016-openstack-kvm-centos-7-go_agent.tgz', $yields[59]->getTarballUrl());
        $this->assertEquals('md5:c16c7925e7dbc82ded9ceca7f2b6c37c', $yields[59]->getTarballChecksum());
        $this->assertEquals(732765531, $yields[59]->getTarballSize());

        $this->assertCount(60, $yields);
    }
}

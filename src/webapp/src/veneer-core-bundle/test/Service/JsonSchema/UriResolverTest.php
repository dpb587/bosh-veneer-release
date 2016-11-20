<?php

namespace Veneer\CoreBundle\Tests\Service\JsonSchema;

use Veneer\CoreBundle\Service\JsonSchema\UriResolver;

class UriResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        $resolver = new UriResolver();
        $this->assertEquals(
            'file://' . realpath(__DIR__ . '/../../../../') . '/veneer-bosh-bundle/src/Resources/schema-map/dev/deployment-v2.json',
            $resolver->resolve('https://dpb587.github.io/bosh-json-schema/default/director/deployment-v2')
        );
    }

    public function testTwo()
    {
        $resolver = new UriResolver();
        $this->assertEquals(
            'file://' . realpath(__DIR__ . '/../../../../') . '/veneer-bosh-bundle/src/Resources/schema-map/dev/deployment-v2.json#/definitions/other',
            $resolver->resolve('https://dpb587.github.io/bosh-json-schema/default/director/deployment-v2#/definitions/other')
        );
    }
}

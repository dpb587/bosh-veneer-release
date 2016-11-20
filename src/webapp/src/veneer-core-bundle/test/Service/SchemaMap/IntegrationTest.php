<?php

namespace Veneer\CoreBundle\Tests\Service\SchemaMap;

use JsonSchema\SchemaStorage;
use JsonSchema\Uri\UriRetriever;
use Veneer\CoreBundle\Service\JsonSchema\UriResolver;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\ArrayDataNode;
use Veneer\CoreBundle\Service\SchemaMap\SchemaMap;

class IntegrationType extends \PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        $source = [
            'director_uuid' => 'something',
            'instance_groups' => [
                [
                    'name' => 'test',
                    'jobs' => [
                        [
                            'release' => 'release-name',
                            'name' => 'job-name',
                        ],
                    ],
                ],
            ],
            'update' => [
                'canaries' => 2,
                'canary_watch_time' => 5000,
                'update_watch_time' => 6000,
                'max_in_flight' => 2,
            ],
        ];

        $root = new ArrayDataNode('');
        $root->setData($source);

        $schema = new SchemaMap(
            new SchemaStorage(
                new UriRetriever(),
                new UriResolver()
            ),
            'https://dpb587.github.io/bosh-json-schema/default/director/deployment-v2'
        );

        $result = $schema->traverse($root, 'instance_groups/name=test');

        $this->assertEquals('/instance_groups/0', $result->getData()->getPath());
        $this->assertEquals('service', $result->getSchema()->getSchema()->properties->lifecycle->default);
        die(print_r($result, true));


        $root->traverse('instance_groups/name=test/jobs/name=job-name/release')->setData('updated');

        $source['instance_groups'][0]['jobs'][0]['release'] = 'updated';

        $this->assertEquals($source, $root->getData());
    }
}

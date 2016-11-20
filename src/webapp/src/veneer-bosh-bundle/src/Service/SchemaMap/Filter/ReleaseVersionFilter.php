<?php

namespace Veneer\BoshBundle\Service\SchemaMap\Filter;

use JsonSchema\SchemaStorage;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\DataNodeInterface;
use Veneer\CoreBundle\Service\SchemaMap\Filter\FilterInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\ArraySchemaNode;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

class ReleaseVersionFilter implements FilterInterface
{
    protected $schemaStorage;

    public function __construct(SchemaStorage $schemaStorage)
    {
        $this->schemaStorage = $schemaStorage;
    }

    public function filter(DataNodeInterface $node, SchemaNodeInterface $schema)
    {
        if (preg_match('#^/instance_groups/[^/]+/jobs/[^/]+/properties$#', $node->getPath())) {
            return $this->loadJobSchema($node->getParent(), 'properties.json');
        }

        throw new \LogicException('Unsupported');
    }

    public function supports(DataNodeInterface $node, SchemaNodeInterface $schema)
    {
        return preg_match('#^/instance_groups/[^/]+/jobs/[^/]+/properties$#', $node->getPath());
    }

    protected function loadJobSchema(DataNodeInterface $jobNode, $schemaPath)
    {
        $jobName = $jobNode->getData()['name'];
        $releaseNode = $jobNode->getRoot()->traverse('releases/name=' . $jobNode->getData()['release']);

        return new ArraySchemaNode(
            $this->schemaStorage->getSchema(
                $this->schemaStorage->getUriResolver()->resolve(
                    sprintf(
                        'veneer://core/release/%s/version/%s/job/%s/%s',
                        $releaseNode->getData()['name'],
                        $releaseNode->getData()['version'],
                        $jobName,
                        $schemaPath
                    )
                )
            )
        );
    }
}

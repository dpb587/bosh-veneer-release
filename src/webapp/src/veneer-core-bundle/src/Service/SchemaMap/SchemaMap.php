<?php

namespace Veneer\CoreBundle\Service\SchemaMap;

use Doctrine\DBAL\Schema\Schema;
use JsonSchema\SchemaStorage;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\DataNodeInterface;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\TraversableDataNodeInterface;
use Veneer\CoreBundle\Service\SchemaMap\Filter\FilterInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\ArraySchemaNode;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

class SchemaMap
{
    protected $jsonSchema;
    protected $filterFactory;
    protected $rootSchemaId;

    public function __construct(SchemaStorage $jsonSchema, FilterInterface $filterFactory, $rootSchemaId)
    {
        $this->jsonSchema = $jsonSchema;
        $this->filterFactory = $filterFactory;
        $this->rootSchemaId = $rootSchemaId;
    }

    protected function getSchema($uri, $baseUri = null)
    {
        return new ArraySchemaNode(
            $this->jsonSchema->getSchema(
                $this->jsonSchema->getUriResolver()->resolve($uri, $baseUri)
            )
        );
    }

    public function traverse(DataNodeInterface $dataNode, $path)
    {
        return $this->traverseNode(
            $this->createFilteredNode(
                $dataNode,
                $this->getSchema($this->rootSchemaId)
            ),
            $path
        );
    }

    protected function traverseNode(Node $node, $path)
    {
        $segments = explode('/', ltrim($path, '/'));

        foreach ($segments as $segment) {
            $dataNode = $node->getData();

            if (!$dataNode instanceof TraversableDataNodeInterface) {
                throw new \InvalidArgumentException('Cannot traverse data node: ' . $dataNode->getPath());
            }

            $traversedDataNode = $dataNode->traverse($segment);
            $traversedSchemaNode = $this->traverseSchema($node->getSchema(), $segment, $dataNode);

            $node = $this->createFilteredNode($traversedDataNode, $traversedSchemaNode);
        }

        return $node;
    }

    protected function createFilteredNode(DataNodeInterface $dataNode, SchemaNodeInterface $schemaNode)
    {
        if ($this->filterFactory->supports($dataNode, $schemaNode)) {
            $schemaNode = $this->filterFactory->filter($dataNode, $schemaNode);
        }

        return new Node($dataNode, $schemaNode);
    }

    protected function traverseSchema(SchemaNodeInterface $schemaNode, $path, DataNodeInterface $dataNode)
    {
        $schema = $schemaNode->getSchema();

        if ($path === '-') {
            return $schemaNode;
        } elseif (is_numeric($path) || (strpos($path, '=') !== false)) {
            if ($schema->type != 'array') {
                throw new \UnexpectedValueException(
                    sprintf(
                        'Expected array for data "%s" in path "%s" of schema "%s", but found "%s"',
                        $dataNode->getPath(),
                        $path,
                        $schemaNode->getSchemaId(),
                        $schema->type
                    )
                );
            }

            return $this->getSchema($this->getSchemaPath($schema->id, '/items'));
        } else {
            if ($schema->type != 'object') {
                throw new \UnexpectedValueException(
                    sprintf(
                        'Expected object for data "%s" in path "%s" of schema "%s", but found "%s"',
                        $dataNode->getPath(),
                        $path,
                        $schemaNode->getSchemaId(),
                        $schema->type
                    )
                );
            }

            return $this->getSchema($this->getSchemaPath($schema->id, '/properties/' . $path));
        }

        throw new \UnexpectedValueException(
            sprintf(
                'Expected something for data "%s" in path "%s" of schema "%s", but found nothing',
                $dataNode->getPath(),
                $path,
                $schemaNode->getSchemaId()
            )
        );
    }

    protected function getSchemaPath($base, $suffix)
    {
        return $base . (strpos($base, '#') ? '' : '#') . $suffix;
    }
}

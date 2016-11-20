<?php

namespace Veneer\CoreBundle\Service\SchemaMap\Filter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\DataNodeInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

class DelegatingContainerFilter implements FilterInterface
{
    protected $container;
    protected $filters;

    public function __construct(ContainerInterface $container, array $filters)
    {
        $this->container = $container;
        $this->filters = $filters;
    }

    public function filter(DataNodeInterface $node, SchemaNodeInterface $schema)
    {
        foreach ($this->filters as $filter) {
            $service = $this->container->get($filter);

            if (!$service->supports($node, $schema)) {
                continue;
            }

            $schema = $service->filter($node, $schema);
        }

        return $schema;
    }

    public function supports(DataNodeInterface $node, SchemaNodeInterface $schema)
    {
        // lazy
        return true;
    }
}

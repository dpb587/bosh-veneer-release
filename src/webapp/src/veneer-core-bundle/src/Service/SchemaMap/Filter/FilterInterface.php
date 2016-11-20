<?php

namespace Veneer\CoreBundle\Service\SchemaMap\Filter;

use Veneer\CoreBundle\Service\SchemaMap\DataNode\DataNodeInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

interface FilterInterface
{
    public function filter(DataNodeInterface $node, SchemaNodeInterface $schema);
    public function supports(DataNodeInterface $node, SchemaNodeInterface $schema);
}

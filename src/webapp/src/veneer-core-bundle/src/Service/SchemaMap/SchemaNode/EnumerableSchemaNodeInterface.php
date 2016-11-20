<?php

namespace Veneer\CoreBundle\Service\SchemaMap\SchemaNode;

use Veneer\CoreBundle\Service\SchemaMap\DataNode\DataNodeInterface;

interface EnumerableSchemaNodeInterface
{
    public function enumerateProperties(DataNodeInterface $data);
}

<?php

namespace Veneer\CoreBundle\Service\SchemaMap;

use Veneer\CoreBundle\Service\SchemaMap\DataNode\DataNodeInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\SchemaNodeInterface;

class Node
{
    protected $data;
    protected $schema;

    public function __construct(DataNodeInterface $data, SchemaNodeInterface $schema)
    {
        $this->data = $data;
        $this->schema = $schema;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getSchema()
    {
        return $this->schema;
    }
}
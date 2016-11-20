<?php

namespace Veneer\CoreBundle\Service\SchemaMap\SchemaNode;

class AbstractSchemaNode implements SchemaNodeInterface
{
    protected $path;
    protected $schema;

    public function __construct(\stdClass $schema)
    {
        $this->schema = $schema;
    }

    public function getSchemaId()
    {
        return $this->schema->id;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getTitle()
    {
        return $this->schema->title;
    }

    public function getDescription()
    {
        return $this->schema->description;
    }

    public function getRequired()
    {
        return $this->schema->required;
    }
}
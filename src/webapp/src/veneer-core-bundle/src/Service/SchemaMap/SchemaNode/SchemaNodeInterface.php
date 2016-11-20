<?php

namespace Veneer\CoreBundle\Service\SchemaMap\SchemaNode;

interface SchemaNodeInterface
{
    public function getSchema();
    public function getSchemaId();

    public function getTitle();
    public function getDescription();
    public function getRequired();
}

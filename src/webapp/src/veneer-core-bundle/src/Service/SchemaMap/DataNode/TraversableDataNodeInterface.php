<?php

namespace Veneer\CoreBundle\Service\SchemaMap\DataNode;

interface TraversableDataNodeInterface
{
    public function traverse($path);
    public function add(DataNodeInterface $node);
}
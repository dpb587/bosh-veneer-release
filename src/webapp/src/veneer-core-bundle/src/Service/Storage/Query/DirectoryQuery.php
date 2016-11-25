<?php

namespace Veneer\CoreBundle\Service\Storage\Query;

use Veneer\CoreBundle\Service\Storage\Object\AbstractObject;
use Veneer\CoreBundle\Service\Storage\Object\Directory;

class DirectoryQuery extends AbstractQuery
{
    protected $children = [];

    public function addChild(AbstractObject $child)
    {
        $this->children[basename($child->getPath())] = $child;

        return $this;
    }

    protected function getResult()
    {
        $result = new Directory($this->getPath());

        foreach ($this->children as $child) {
            $result->addChild($child);
        }

        return $result;
    }
}

<?php

namespace Veneer\CoreBundle\Service\Storage\Object;

class Directory extends AbstractObject
{
    protected $children = [];

    public function getObjectType()
    {
        return static::OBJECT_TYPE_DIR;
    }

    public function addChild(AbstractObject $child)
    {
        $this->children[$child->getBasename()] = $child;

        return $this;
    }

    public function getChildren()
    {
        return $this->children;
    }
}
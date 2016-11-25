<?php

namespace Veneer\CoreBundle\Service\Storage\Object;

abstract class AbstractObject
{
//    const OBJECT_LOGICAL = 'logical';
//    const OBJECT_PHYSICAL = 'physical';
    const OBJECT_TYPE_DIR = 'dir';
    const OBJECT_TYPE_FILE = 'file';

    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getBasename()
    {
        return basename($this->getPath());
    }

    abstract public function getObjectType();
}
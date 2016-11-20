<?php

namespace Veneer\CoreBundle\Service\SchemaMap\DataNode;

abstract class AbstractDataNode implements DataNodeInterface
{
    protected $parent;
    protected $path;
    protected $data;
    protected $dataExists = false;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getRoot()
    {
        return $this->parent ? $this->parent->getRoot() : $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getRelativePath()
    {
        return $this->path;
    }

    public function getPath()
    {
        return ($this->parent ? $this->parent->getPath() . '/' : '') . $this->getRelativePath();
    }

    public function setData($data)
    {
        $this->data = $data;
        $this->dataExists = true;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function unsetData()
    {
        $this->data = null;
        $this->dataExists = false;

        return $this;
    }

    public function hasData()
    {
        return $this->dataExists;
    }

    public function applyData($data)
    {
        $data[$this->getRelativePath()] = $this->getData();

        return $data;
    }

    protected function setParent(TraversableDataNodeInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }
}

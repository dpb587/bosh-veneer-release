<?php

namespace Veneer\CoreBundle\Service\Workspace\Repository;

class Changeset implements \IteratorAggregate
{
    const CREATED = 'created';
    const DELETED = 'deleted';
    const MODIFIED = 'modified';

    protected $oldTree;
    protected $newTree;
    protected $changes;

    public function __construct(TreeInterface $oldTree, TreeInterface $newTree, array $changes)
    {
        $this->oldTree = $oldTree;
        $this->newTree = $newTree;
        $this->changes = $changes;
    }

    public function getOldTree()
    {
        return $this->oldTree;
    }

    public function getOldBlob($path)
    {
        return $this->oldTree->getBlob($path);
    }

    public function getNewTree()
    {
        return $this->newTree;
    }

    public function getNewBlob($path)
    {
        return $this->newTree->getBlob($path);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->changes);
    }
}

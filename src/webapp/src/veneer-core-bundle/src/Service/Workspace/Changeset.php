<?php

namespace Veneer\CoreBundle\Service\Workspace;

class Changeset implements \IteratorAggregate
{
    const CREATED = 'created';
    const DELETED = 'deleted';
    const MODIFIED = 'modified';

    protected $repo;
    protected $oldRef;
    protected $newRef;
    protected $changes;

    public function __construct(GitRepository $repo, $oldRef, $newRef, array $changes)
    {
        $this->repo = $repo;
        $this->oldRef = $oldRef;
        $this->newRef = $newRef;
        $this->changes = $changes;
    }

    public function getChange($path)
    {
        return $this->changes[$path];
    }

    public function getOldRef()
    {
        return $this->oldRef;
    }

    public function getNewRef()
    {
        return $this->newRef;
    }

    public function showOldFile($path)
    {
        return $this->repo->showFile($path, $this->oldRef);
    }

    public function showNewFile($path)
    {
        return $this->repo->showFile($path, $this->newRef);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->changes);
    }
}

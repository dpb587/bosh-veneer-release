<?php

namespace Veneer\Component\Workspace\GitRepository;

use Veneer\Component\Workspace\TreeInterface;

class Tree implements TreeInterface
{
    protected $workspace;
    protected $name;
    protected $blobs = [];

    public function __construct(Workspace $workspace, $name)
    {
        $this->workspace = $workspace;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCanonicalName()
    {

    }

    public function getWorkspace()
    {
        return $this->workspace;
    }

    public function getBlob($path)
    {
        if (!isset($this->blobs[$path])) {
            $this->blobs[$path] = new Blob($this, $path);
        }

        return $this->blobs[$path];
    }

    public function commit($message)
    {
        // @todo
    }

    public function merge(TreeInterface $tree, $message = null)
    {
        // @todo
    }
}

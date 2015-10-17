<?php

namespace Veneer\Component\Workspace\GitRepository;

use Veneer\Component\Workspace\WorkspaceInterface;

class Workspace implements WorkspaceInterface
{
    protected $root;
    protected $options;

    public function __construct($root, array $options)
    {
        $this->root = $root;
        $this->options = array_merge(
            [
                'default_tree' => 'master',
                'git_exec' => 'git',
            ],
            $options
        );
    }

    public function getBlob($path)
    {
        return $this->getTree($this->getDefaultTree())->getBlob($path);
    }

    public function getTree($name)
    {
        return new Tree($this, $name);
    }

    public function getDefaultTree()
    {
        return $this->options['default_tree'];
    }

    public function hasTreeSupport()
    {
        return true;
    }

    public function hasHistorySupport()
    {
        return true;
    }
}

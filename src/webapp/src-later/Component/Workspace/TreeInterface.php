<?php

namespace Veneer\Component\Workspace;

interface TreeInterface
{
    public function getName();
    public function getCanonicalName();
    public function getWorkspace();

    public function getBlob($path);
    public function commit($message);
    public function merge(TreeInterface $tree, $message = null);
}

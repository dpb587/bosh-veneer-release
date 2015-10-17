<?php

namespace Veneer\Component\Workspace;

interface WorkspaceInterface
{
    public function getBlob($path);
    public function getTree($name);
    public function getDefaultTree();
    public function hasTreeSupport();
    public function hasHistorySupport();
}

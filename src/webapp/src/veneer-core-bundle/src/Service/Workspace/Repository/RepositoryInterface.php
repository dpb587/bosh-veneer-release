<?php

namespace Veneer\CoreBundle\Service\Workspace\Repository;

interface RepositoryInterface
{
    public function getBlob($path);
    public function getTree($name);
    public function getDefaultTree();
}

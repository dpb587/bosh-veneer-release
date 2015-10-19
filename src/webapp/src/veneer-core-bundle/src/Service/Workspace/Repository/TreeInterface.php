<?php

namespace Veneer\CoreBundle\Service\Workspace\Repository;

interface TreeInterface
{
    public function getName();
    public function getCanonicalName();
    public function getRepository();

    public function getBlob($path);
    public function commit($message);
    public function merge(TreeInterface $tree, $message = null);
}

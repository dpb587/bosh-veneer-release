<?php

namespace Veneer\CoreBundle\Service\Workspace\Repository\GitRepository;

use Veneer\CoreBundle\Service\Workspace\Repository\TreeInterface;

class Tree implements TreeInterface
{
    protected $repository;
    protected $name;
    protected $canonicalName;
    protected $blobs = [];

    public function __construct(Repository $repository, $name)
    {
        $this->repository = $repository;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCanonicalName()
    {
        if (null === $this->canonicalName) {
            try {
                $this->canonicalName = trim($this->getRepository()->exec([ 'rev-parse', $this->getName() ]));
            } catch (\Exception $e) {
                throw new \UnexpectedValueException(sprintf('Reference could not be resolved for "%s"', $this->getName()), null, $e);
            }
        }

        return $this->canonicalName;
    }

    public function getRepository()
    {
        return $this->repository;
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

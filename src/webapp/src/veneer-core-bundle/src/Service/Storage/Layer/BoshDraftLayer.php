<?php

namespace Veneer\CoreBundle\Service\Storage\Layer;

use Veneer\CoreBundle\Service\Storage\Query\AbstractQuery;
use Veneer\CoreBundle\Service\Storage\Query\DirectoryQuery;
use Veneer\CoreBundle\Service\Storage\Query\FileQuery;

class BoshDraftLayer implements LayerInterface
{
    protected $patch;

    public function get(FileQuery $query)
    {
        if (!$this->isApplicableQuery($query)) return;

        $result = $query->execute();

        // @todo patch with patch layer

        $query->setFile($result);
    }

    public function put(FileQuery $query)
    {
        if (!$this->isApplicableQuery($query)) return;

        // @todo implement...
        $existingFile = $query->getSystem()->get($query->getPath());
        $newFile = $query->getFile();

        $existingOps = $this->readPatch($query->getPath());
        $newOps = $this->generateDiff($existingFile, $newFile);

        $this->writePatch($query->getPath(), array_merge($existingOps, $newOps));

        $query->successful();
    }

    public function ls(DirectoryQuery $query)
    {
        if (!$this->isApplicableQuery($query)) return;

        $result = $query->execute();

        // @todo enumerate additions
        // @todo enumerate deletions

        $query->successful();
    }

    public function rm(FileQuery $query)
    {
        if (!$this->isApplicableQuery($query)) return;

        // @todo verify it exists first?
        // @todo add a delete marker

        $query->successful();
    }

    protected function isApplicableQuery(AbstractQuery $query)
    {
        if (!preg_match('#^/bosh(/.+|)$#', $query->getPath())) {
            // only care about bosh path
            return false;
        } elseif ($query->getContextKey('ref') == 'active') {
            // only care about other refs (never patching live)
            return false;
        }

        return true;
    }
}

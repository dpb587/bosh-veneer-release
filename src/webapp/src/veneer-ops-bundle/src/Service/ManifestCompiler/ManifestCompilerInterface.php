<?php

namespace Veneer\OpsBundle\Service\ManifestCompiler;

use Veneer\CoreBundle\Service\Workspace\Repository\BlobInterface;

interface ManifestCompilerInterface
{
    /**
     * Compile a deployment manifest file. Multiple compilers may be run on a single manifest.
     * 
     * @param BlobInterface $manifest The source repository object
     * @param string $compile The manifest data needing compilation
     * @return string The compiled manifest data
     */
    public function compile(BlobInterface $manifest, $compile);
}

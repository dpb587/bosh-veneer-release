<?php

namespace Veneer\OpsBundle\Service\ManifestBuilder;

interface ManifestBuilderInterface
{
    /**
     * Compile a manifest file. Multiple compilers may be run on a single manifest.
     * 
     * @param string $cwd The working directory
     * @param string $manifestPath The manifest path
     * @return string The compiled manifest
     */
    public function build($cwd, $manifestPath);
}

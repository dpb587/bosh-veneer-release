<?php

namespace Veneer\OpsBundle\Service\ManifestCompiler;

use Veneer\CoreBundle\Service\Workspace\Repository\BlobInterface;

class VeneerManifestCompiler implements ManifestCompilerInterface
{
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function compile(BlobInterface $manifest)
    {
        
    }
}

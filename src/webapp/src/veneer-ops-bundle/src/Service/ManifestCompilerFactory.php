<?php

namespace Veneer\OpsBundle\Service;

use Veneer\CoreBundle\DependencyInjection\ContainerMap;
use Veneer\CoreBundle\Service\Workspace\Repository\BlobInterface;

class ManifestCompilerFactory extends ContainerMap
{
    public function compile(BlobInterface $manifest)
    {
        $compile = $manifest->data();

        foreach ($this->all() as $service) {
            $compile = $service->compile($manifest, $compile);
        }

        return $compile;
    }
}

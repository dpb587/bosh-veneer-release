<?php

namespace Veneer\OpsBundle\Service\ManifestCompiler;

use Veneer\CoreBundle\Service\Workspace\Repository\BlobInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Process\Process;

class ErbManifestCompiler implements ManifestCompilerInterface
{
    protected $erbPath;

    public function __construct($erbPath)
    {
        $this->erbPath = $erbPath;
    }

    public function compile(BlobInterface $manifest, $compile)
    {
        $compileArray = Yaml::parse($compile);

        if (!isset($compileArray['_spiff']['files'])) {
            return $compile;
        }

        $p = new Process(
            implode(
                ' ',
                array_map(
                    'escapeshellarg',
                    array_merge(
                        [
                            $this->spiffPath,
                            'merge',
                        ],
                        $compileArray['_spiff']['files']
                    )
                )
            )
        );

        $p->mustRun();

        $compiledRaw = $p->getOutput();
        $compiledArray = Yaml::parse($compiledRaw);

        unset($compiledArray['_spiff']);

        return Yaml::dump($compiledArray, 8);
    }
}

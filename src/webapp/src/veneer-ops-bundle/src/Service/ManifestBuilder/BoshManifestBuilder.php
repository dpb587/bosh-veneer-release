<?php

namespace Veneer\OpsBundle\Service\ManifestBuilder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Veneer\CoreBundle\Service\Workspace\Repository\BlobInterface;

class BoshManifestBuilder implements ManifestBuilderInterface
{
    protected $executable;

    public function __construct($executable)
    {
        $this->executable = $executable;
    }

    public function build($cwd, $manifestPath)
    {
        if (!file_exists($cwd . '/' . $manifestPath)) {
            throw new \InvalidArgumentException('File does not exist: ' . $manifestPath);
        }

        $manifestDir = dirname($manifestPath);

        $args = [
            $this->executable,
            'build-manifest',
        ];

        if (is_dir($cwd . '/' . $manifestDir . '/ops')) {
            foreach ((new Finder())->in($cwd . '/' . $manifestDir . '/ops')->name('*.yml')->sortByName() as $ops) {
                $args[] = ' -o ' . $ops->getRelativePath();
            }
        }

        if (is_dir($cwd . '/' . $manifestDir . '/vars')) {
            foreach ((new Finder())->in($cwd . '/vars')->name('*.yml')->sortByName() as $vars) {
                $args[] = ' -l ' . $vars->getRelativePath();
            }
        }

        $args[] = $manifestPath;

        $p = new Process(
            sprintf(
                '%s build-manifest %s',
                $this->executable,
                escapeshellarg($manifestPath)
            ),
            $cwd
        );

        $p->mustRun();

        return $p->getOutput();
    }
}

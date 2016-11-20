<?php

namespace Veneer\BoshEditorBundle\Service\ManifestBuilder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class BoshManifestBuilder implements ManifestBuilderInterface
{
    const REGEX_VARIABLE = '/\(\(!?([-\w\p{L}]+)\)\)/';

    protected $executable;

    public function __construct($executable)
    {
        $this->executable = $executable;
    }

    public function build($cwd, $manifestPath)
    {
        if (!file_exists($cwd.'/'.$manifestPath)) {
            throw new \InvalidArgumentException('File does not exist: '.$manifestPath);
        }

        $manifestDir = dirname($manifestPath);

        $args = [
            $this->executable,
            'build-manifest',
        ];

        if (is_dir($cwd.'/'.$manifestDir.'/ops')) {
            foreach ((new Finder())->in($cwd.'/'.$manifestDir.'/ops')->name('*.yml')->sortByName() as $ops) {
                $args[] = ' -o '.$ops->getRelativePath();
            }
        }

        if (is_dir($cwd.'/'.$manifestDir.'/vars')) {
            foreach ((new Finder())->in($cwd.'/vars')->name('*.yml')->sortByName() as $vars) {
                $args[] = ' -l '.$vars->getRelativePath();
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

    public function findMissingParameters(array $manifestHash)
    {
        return $this->findMissingParametersDeep($manifestHash);
    }

    private function findMissingParametersDeep(array $data, $path = '')
    {
        $missing = [];

        $pathMethod = $this->findPathMethod($data);

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $match = preg_match_all(static::REGEX_VARIABLE, $value, $placeholders, PREG_SET_ORDER);

                if (!$match) {
                    continue;
                }

                foreach ($placeholders as $placeholder) {
                    $missing[$placeholder[1]][] = $this->appendPath($path, $data, $pathMethod, $key);
                }
            } elseif (is_array($value)) {
                foreach ($this->findMissingParametersDeep($value, $this->appendPath($path, $data, $pathMethod, $key)) as $placeholder => $subpaths) {
                    foreach ($subpaths as $subpath) {
                        $missing[$placeholder][] = $subpath;
                    }
                }
            }
        }

        return $missing;
    }

    private function findPathMethod(array $data)
    {
        $dataCount = count($data);
        $byName = array_filter(
            $data,
            function ($value) {
                return is_array($value) && isset($value['name']);
            }
        );

        if (count($byName) == $dataCount) {
            return 'name';
        }

        return 'index';
    }

    private function appendPath($basePath, array $data, $pathMethod, $key)
    {
        if ('name' == $pathMethod) {
            return $basePath.'/name='.$data[$key]['name'];
        }

        return $basePath.'/'.$key;
    }
}

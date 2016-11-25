<?php

namespace Veneer\CoreBundle\Service\Storage\Layer;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Veneer\CoreBundle\Service\Storage\Object\Directory;
use Veneer\CoreBundle\Service\Storage\Object\File;
use Veneer\CoreBundle\Service\Storage\Query\DirectoryQuery;
use Veneer\CoreBundle\Service\Storage\Query\FileQuery;

class GitRepositoryLayer implements LayerInterface
{
    protected $workingTree;
    protected $pathPrefix;
    protected $gitExecutable;

    public function __construct($workingTree, $pathPrefix = '', $gitExecutable = 'git')
    {
        $this->workingTree = $workingTree;
        $this->pathPrefix = $pathPrefix;
        $this->gitExecutable = $gitExecutable;
    }

    protected function matchPath($path)
    {
        return preg_match('#^' . preg_quote($this->pathPrefix, '#') . '(/|$)#', $path);
    }

    public function get(FileQuery $query)
    {
        if (!$this->matchPath($query->getPath())) return;

        $path = $this->workingTree . '/' . $query->getPath();

        if (!file_exists($path)) return;

        $query->setFile(
            (new File($path))
                ->setData(file_get_contents($path))
        );

        return $query->successful();
    }

    public function put(FileQuery $query)
    {
        if (!$this->matchPath($query->getPath())) return;

        $path = $this->workingTree . '/' . $query->getPath();

        mkdir(dirname($path), 0700, true);

        if ($query->getFile()->getData() == file_get_contents($path)) {
            return $query->successful();
        }

        file_put_contents($path, $query->getFile()->getData());

        ProcessBuilder::create([$this->gitExecutable, 'add', $query->getPath()])
            ->setWorkingDirectory($this->workingTree)
            ->getProcess()
            ->mustRun();

        ProcessBuilder::create([$this->gitExecutable, 'commit', '-m', 'commit', $query->getPath()])
            ->setWorkingDirectory($this->workingTree)
            ->getProcess()
            ->mustRun();

        return $query->successful();
    }

    public function ls(DirectoryQuery $query)
    {
        if (!$this->matchPath($query->getPath())) return;

        $path = $this->workingTree . '/' . $query->getPath();

        if (!file_exists($path) || !is_dir($path)) {
            return;
        }

        foreach (Finder::create()->in($path)->depth(0) as $item) {
            if ($item->isDir()) {
                $query->addChild(new Directory($query->getPath() . '/' . $item->getBasename()));
            } else {
                $query->addChild(new File($query->getPath() . '/' . $item->getBasename()));
            }
        }

        return $query->successful();
    }

    public function rm(FileQuery $query)
    {
        if (!$this->matchPath($query->getPath())) return;

        $path = $this->workingTree . '/' . $query->getPath();

        if (!file_exists($path)) {
            return $query->successful();
        }

        ProcessBuilder::create([$this->gitExecutable, 'rm', $query->getPath()])
            ->setWorkingDirectory($this->workingTree)
            ->getProcess()
            ->mustRun();

        ProcessBuilder::create([$this->gitExecutable, 'commit', '-m', 'commit', $query->getPath()])
            ->setWorkingDirectory($this->workingTree)
            ->getProcess()
            ->mustRun();

        return $query->successful();
    }
}
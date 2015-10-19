<?php

namespace Veneer\CoreBundle\Service\Workspace;

use Symfony\Component\Process\Process;
use Veneer\CoreBundle\Service\Workspace\Changeset;
use TQ\Git\Repository\Repository;
use TQ\Git\Cli\Binary;
use TQ\Vcs\Gaufrette\Adapter;

class GitRepository extends Repository
{
    protected $pathPrefix;

    public function __construct($root, $pathPrefix, $git)
    {
        $this->pathPrefix = rtrim($pathPrefix, '/');

        parent::__construct($root, Binary::ensure($git));
    }

    public function getGaufrette()
    {
        return new Adapter($this);
    }

    public function listDirectory($directory = '.', $ref = 'HEAD')
    {
        return parent::listDirectory($this->getPrefixedPath($directory), $ref);
    }

    public function showFile($file, $ref = 'HEAD')
    {
        return parent::showFile($this->getPrefixedPath($file), $ref);
    }

    public function writeFile($path, $data, $commitMsg = null, $fileMode = null, $dirMode = null, $recursive = true, $author = null)
    {
        return parent::writeFile(
            $this->getPrefixedPath($path),
            $data,
            $commitMsg,
            $fileMode,
            $dirMode,
            $recursive,
            $author
        );
    }

    public function removeFile($path, $commitMsg = null, $recursive = false, $force = false, $author = null)
    {
        return parent::removeFile(
            $this->getPrefixedPath($path),
            $commitMsg,
            $recursive,
            $force,
            $author
        );
    }

    public function renameFile($path, $commitMsg = null, $recursive = false, $force = false, $author = null)
    {
        return parent::renameFile(
            $this->getPrefixedPath($path),
            $commitMsg,
            $recursive,
            $force,
            $author
        );
    }

    public function getPrefixedPath($path)
    {
        return ltrim($this->pathPrefix . '/' . $path, '/');
    }

    public function diff($oldRef, $newRef)
    {
        $prefixedPath = $this->getPrefixedPath('');
        $strlen = strlen($prefixedPath);

        $lines = explode(
            "\n",
            trim($this->exec(
                'diff',
                [
                    '--name-status',
                    $oldRef,
                    $newRef,
                    '--',
                    $prefixedPath,
                ]
            ))
        );

        $statusMap = [
            'A' => Changeset::CREATED,
            'M' => Changeset::MODIFIED,
            'D' => Changeset::DELETED,
        ];

        $changes = [];

        if ((1 != count($lines)) && ('' != $lines[0])) {
            foreach ($lines as $line) {
                $sp = preg_split('/\s+/', $line, 2);

                $changes[ltrim(substr($sp[1], $strlen), '/')] = $statusMap[$sp[0]];
            }
        }

        return new Changeset($this, $oldRef, $newRef, $changes);
    }

    public function exec($command, array $arguments = [], $stdin = null)
    {
        $call = $this->git->createCall(
            $this->getRepositoryPath(),
            $command,
            $arguments
        );

        $p = new Process(
            $call->getCmd(),
            $call->getCwd(),
            $call->getEnv(),
            $stdin
        );

        $p->mustRun();

        return $p->getOutput();
    }
}

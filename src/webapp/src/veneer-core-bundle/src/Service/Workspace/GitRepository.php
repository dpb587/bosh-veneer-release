<?php

namespace Veneer\CoreBundle\Service\Workspace;

use Symfony\Component\Process\Process;
use Veneer\CoreBundle\Service\Workspace\Changeset;
use TQ\Git\Repository\Repository;
use TQ\Git\Cli\Binary;
use TQ\Vcs\Gaufrette\Adapter;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;

class GitRepository extends Repository
{
    protected $binary;
    protected $pathPrefix;

    public function __construct($root, $pathPrefix, $git)
    {
        $this->pathPrefix = rtrim($pathPrefix, '/');
        $this->binary = $git;

        parent::__construct($root, Binary::ensure($git));
    }

    public function createCheckout($ref = 'master', $mode = 0)
    {
        if (!$mode & CheckoutInterface::MODE_WRITABLE) {
            $checkout = new Checkout\GitDirCheckout(
                $this->binary,
                $this->getRepositoryPath() . '/.git',
                $ref,
                $mode
            );
        } else {
            $mode = $mode | CheckoutInterface::MODE_DESTROYABLE | CheckoutInterface::MODE_DESTRUCT_DESTROY;

            $tmp = uniqid('/tmp/gitrepo-' . microtime(true) . '-');

            $call = $this->getGit()->createCall(
                $tmp,
                'clone',
                [
                    '--no-checkout',
                    $this->getRepositoryPath(),
                    $tmp,
                ]
            );

            $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
            $p->mustRun();

            $call = $this->getGit()->createCall(
                $tmp,
                'checkout',
                [
                    $ref,
                ]
            );

            $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
            $p->mustRun();

            $checkout = new Checkout\PhysicalCheckout($tmp, $ref, $mode);
        }

        $checkout->cd($this->pathPrefix);

        return $checkout;
    }

    public function commitCheckout(Checkout\PhysicalCheckout $checkout, $message, array $options = [])
    {
        $commitEnv = [
            'GIT_AUTHOR_NAME' => $options['author']['name'],
            'GIT_AUTHOR_EMAIL' => $options['author']['email'],
            'GIT_COMMITTER_NAME' => $options['author']['name'],
            'GIT_COMMITTER_EMAIL' => $options['author']['email'],
        ];

        // check if dirty

        $call = $this->getGit()->createCall(
            $checkout->getPhysicalPath(),
            'status',
            [
                '--porcelain',
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), array_merge($call->getEnv() ?: [], $commitEnv));
        $p->mustRun();

        if ('' == $p->getOutput()) {
            return null;
        }

        // add all changes

        $call = $this->getGit()->createCall(
            $checkout->getPhysicalPath(),
            'add',
            [
                '-A',
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), array_merge($call->getEnv() ?: [], $commitEnv));
        $p->mustRun();

        // commit

        $call = $this->getGit()->createCall(
            $checkout->getPhysicalPath(),
            'commit',
            [
                '-a',
                '-m', $message,
            ]
        );

        $p = new Process($call->getCmd(), $call->getCwd(), array_merge($call->getEnv() ?: [], $commitEnv));
        $p->mustRun();

        // push it back to the main workspace

        $pushArgs = [
            'origin',
            $checkout->getHead() . (isset($options['branch']) ? (':' . $options['branch']) : ''),
        ];

        if (!empty($options['force'])) {
            array_unshift($pushArgs, '--force');
        }

        $call = $this->getGit()->createCall(
            $checkout->getPhysicalPath(),
            'push',
            $pushArgs
        );

        $p = new Process($call->getCmd(), $call->getCwd(), $call->getEnv());
        $p->mustRun();

        return true;
    }

    public function listDirectory($directory = '.', $ref = 'HEAD')
    {
        return parent::listDirectory($this->getPrefixedPath($directory), $ref);
    }

    public function showFile($file, $ref = 'HEAD')
    {
        return parent::showFile($this->getPrefixedPath($file), $ref);
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

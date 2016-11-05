<?php

namespace Veneer\CoreBundle\Service\Workspace\Checkout;

use Symfony\Component\Process\Process;

class GitDirCheckout implements CheckoutInterface
{
    protected $binary;
    protected $path;
    protected $head;
    protected $mode;
    protected $cwd = '/';

    public function __construct($binary, $path, $head, $mode = 0)
    {
        if ($mode & CheckoutInterface::MODE_WRITABLE) {
            throw new \LogicException('Cannot write to git-dir checkout');
        }

        $this->binary = $binary;
        $this->path = $path;
        $this->head = $head;
        $this->mode = $mode;
    }

    public function getPhysicalCheckout()
    {
        $mode = $this->mode | CheckoutInterface::MODE_WRITABLE;# | CheckoutInterface::MODE_DESTROYABLE | CheckoutInterface::MODE_DESTRUCT_DESTROY;

        $tmp = uniqid('/tmp/gitrepo-' . microtime(true) . '-');

        $p = new Process(
            sprintf(
                '%s clone --no-checkout %s %s',
                $this->binary,
                $this->path,
                $tmp
            )
        );

        $p->mustRun();

        $p = new Process(
            sprintf(
                '%s checkout %s',
                $this->binary,
                $this->head
            ),
            $tmp
        );

        $p->mustRun();

        return new PhysicalCheckout($tmp, $this->head, $mode);
    }

    public function getHead()
    {
        return $this->head;
    }

    public function cd($path)
    {
        $this->cwd = $this->resolvePath($path);

        return $this;
    }

    public function ls($path)
    {
        $physical = $this->resolvePath($path);

        $p = new Process(sprintf(
            '%s --git-dir=%s ls-tree %s %s',
            escapeshellarg($this->binary),
            escapeshellarg($this->path),
            escapeshellarg($this->head),
            escapeshellarg($physical . '/')
        ));

        $p->mustRun();

        $ls = [];

        foreach (explode("\n", trim($p->getOutput())) as $line) {
            $parts = preg_split('/\s+/', $line, 4);

            $ls[] = [
                'name' => basename($parts[3]),
                'type' => ('tree' == $parts[1]) ? 'dir' : (('2' == $parts[0][1]) ? 'link' : 'file'),
            ];
        }

        return $ls;
    }

    public function get($path)
    {
        $physical = $this->resolvePath($path);

        $p = new Process(sprintf(
            '%s --git-dir=%s show %s:%s',
            escapeshellarg($this->binary),
            escapeshellarg($this->path),
            escapeshellarg($this->head),
            escapeshellarg($physical)
        ));

        $p->run();

        if (128 == $p->getExitCode()) {
            throw new \InvalidArgumentException(sprintf('Path does not exist: %s', $physical));
        }

        $data = $p->getOutput();

        if (preg_match('#^' . preg_quote(sprintf('tree %s:%s', $this->head, $physical), '#') . '\n#', $data)) {
            throw new \InvalidArgumentException(sprintf('Path is not a file: %s', $physical));
        }

        return $data;
    }

    public function exists($path)
    {
        $physical = $this->resolvePath($path);

        $p = new Process(sprintf(
            '%s --git-dir=%s show %s:%s',
            escapeshellarg($this->binary),
            escapeshellarg($this->path),
            escapeshellarg($this->head),
            escapeshellarg($physical)
        ));

        $p->run();

        if (0 == $p->getExitCode()) {
            return true;
        } elseif (128 == $p->getExitCode()) {
            return false;
        }

        throw new \RuntimeException('Failed to check for file existence');
    }

    public function put($path, $data, $mode = 0600)
    {
        throw new \LogicException('Checkout is not writable.');
    }

    public function delete($path)
    {
        throw new \LogicException('Checkout is not writable.');
    }

    protected function resolvePath($path)
    {
        $resolved = $this->cwd;

        foreach (explode('/', $path) as $dir) {
            if ('' == $dir) {
                continue;
            } elseif ('.' == $dir) {
                continue;
            } elseif ('..' == $dir) {
                if ('/' == $resolved) {
                    throw new \OutOfBoundsException();
                }

                $resolved = dirname($resolved);
            } else {
                $resolved .= '/' . $dir;
            }
        }

        return ltrim($resolved, '/');
    }
}

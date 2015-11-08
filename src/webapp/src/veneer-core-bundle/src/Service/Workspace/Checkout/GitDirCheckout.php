<?php

namespace Veneer\CoreBundle\Service\Workspace\Checkout;

use Symfony\Component\Process\Process;

class GitDirCheckout implements CheckoutInterface
{
    protected $binary;
    protected $path;
    protected $ref;
    protected $mode;
    protected $cwd = '/';

    public function __construct($binary, $path, $ref, $mode = 0)
    {
        if ($mode & CheckoutInterface::MODE_WRITABLE) {
            throw new \LogicException('Cannot write to git-dir checkout');
        }

        $this->binary = $binary;
        $this->path = $path;
        $this->ref = $ref;
        $this->mode = $mode;
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
            escapeshellarg($this->ref),
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
            escapeshellarg($this->ref),
            escapeshellarg($physical)
        ));

        $p->run();

        if (128 == $p->getExitCode()) {
            throw new \InvalidArgumentException(sprintf('Path does not exist: %s', $physical));
        }

        $data = $p->getOutput();

        if (preg_match('#^' . preg_quote(sprintf('tree %s:%s', $this->ref, $physical), '#') . '\n#', $data)) {
            throw new \InvalidArgumentException(sprintf('Path is not a file: %s', $physical));
        }

        return $data;
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

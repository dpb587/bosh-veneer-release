<?php

namespace Veneer\CoreBundle\Service\Workspace\Checkout;

class PhysicalCheckout implements CheckoutInterface
{
    protected $path;
    protected $head;
    protected $mode;
    protected $cwd = '/';

    public function __construct($path, $head, $mode = 0)
    {
        $this->path = $path;
        $this->head = $head;
        $this->mode = $mode;
    }

    public function getHead()
    {
        return $this->head;
    }

    public function getPhysicalPath()
    {
        return $this->path;
    }

    public function cd($path)
    {
        $this->cwd = $this->resolvePath($path);

        return $this;
    }

    public function exists($path)
    {
        return file_exists($path);
    }

    public function ls($path)
    {
        $physical = $this->path . '/' . $this->resolvePath($path);

        $dh = opendir($physical);
        $ls = [];

        while (false !== $name = readdir($dh)) {
            if (('.' == $name) || ('..' == $name)) {
                continue;
            }

            $ls[] = [
                'name' => $name,
                'type' => is_link($physical . '/' . $name) ? 'link' : (is_dir($physical . '/' . $name) ? 'dir' : 'file'),
            ];
        }

        closedir($dh);

        return $ls;
    }

    public function get($path)
    {
        $physical = $this->path . '/' . $this->resolvePath($path);

        if (!file_exists($physical)) {
            throw new \InvalidArgumentException(sprintf('Path does not exist: %s', $physical));
        } elseif (!is_file($physical)) {
            throw new \InvalidArgumentException(sprintf('Path is not a file: %s', $physical));
        }

        return file_get_contents($physical);
    }

    public function put($path, $data, $mode = 0600)
    {
        if (!$this->mode & CheckoutInterface::MODE_WRITABLE) {
            throw new \LogicException('Checkout is not writable.');
        }

        $physical = $this->path . '/' . $this->resolvePath($path);
        $dir = dirname($physical);

        if (!file_exists($dir)) {
            mkdir($dir, $mode, true);
        }

        file_put_contents($physical, $data);
        chmod($physical, $mode);

        return $this;
    }

    public function delete($path)
    {
        if (!$this->mode & CheckoutInterface::MODE_WRITABLE) {
            throw new \LogicException('Checkout is not writable.');
        }

        $physical = $this->path . '/' . $this->resolvePath($path);

        if (is_dir($physical)) {
            exec(sprintf('rm -fr %s', $physical));
        } elseif (is_file($physical)) {
            unlink($physical);
        }

        return $this;
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

        return $resolved;
    }

    public function destroy()
    {
        if (!$this->mode & CheckoutInterface::MODE_DESTROYABLE) {
            throw new \LogicException('Checkout is not destroyable.');
        }

        exec(sprintf('rm -fr %s', escapeshellarg($this->path)));
    }

    public function __destruct()
    {
        if ($this->mode & CheckoutInterface::MODE_DESTRUCT_DESTROY) {
            $this->destroy();
        }
    }
}

<?php

namespace Veneer\CoreBundle\Service\Workspace\Checkout;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Exception\LogicException;

class LiveCheckout implements CheckoutInterface
{
    protected $em;
    protected $mode;
    protected $cwd = '/';

    public function __construct(EntityManager $em, $mode)
    {
        $this->em = $em;
        $this->mode = $mode;
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

        $ls = [];

        if ($physical == '') {
            $ls[] = [
                'name' => 'deployments',
                'type' => 'dir',
            ];

            $ls[] = [
                'name' => 'cloud-config.yml',
                'type' => 'file',
            ];

            $ls[] = [
                'name' => 'runtime-config.yml',
                'type' => 'file',
            ];
        } elseif ($physical == 'deployments') {
            foreach ($this->em->getRepository('VeneerBoshBundle:Deployments')->findAll() as $deployment) {
                $ls[] = [
                    'name' => sprintf('%s.yml', $deployment['name']),
                    'type' => 'file',
                ];
            }
        }

        return $ls;
    }

    public function get($path)
    {
        $physical = $this->resolvePath($path);

        if ($physical == 'cloud-config.yml') {
            $cloudConfig = $this->em->getRepository('VeneerBoshBundle:CloudConfigs')->findOneBy([], ['id' => 'DESC']);

            return $cloudConfig['properties'];
        } elseif ($physical == 'runtime-config.yml') {
            $runtimeConfig = $this->em->getRepository('VeneerBoshBundle:RuntimeConfigs')->findOneBy([], ['id' => 'DESC']);

            return $runtimeConfig['properties'];
        } elseif (preg_match('#^deployments/([^\.]+)\.yml$#', $path, $match)) {
            $deployment = $this->em->getRepository('VeneerBoshBundle:Deployments')->findOneBy(['name' => $match[1]]);

            if (!$deployment) {
                throw new \InvalidArgumentException(sprintf('Path does not exist: %s', $physical));
            }

            return $deployment['manifest'];
        } else {
            throw new \InvalidArgumentException(sprintf('Path does not exist: %s', $physical));
        }
    }

    public function put($path, $data, $mode = 0600)
    {
        throw new \LogicException('@todo');
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
        throw new \LogicException('@todo');
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

        return ltrim($resolved, '/');
    }

    public function destroy()
    {
        // done
    }

    public function __destruct()
    {
        if ($this->mode & CheckoutInterface::MODE_DESTRUCT_DESTROY) {
            $this->destroy();
        }
    }
}

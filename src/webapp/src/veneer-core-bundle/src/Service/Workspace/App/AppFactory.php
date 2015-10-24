<?php

namespace Veneer\CoreBundle\Service\Workspace\App;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AppFactory
{
    protected $container;
    protected $map;

    public function __construct(ContainerInterface $container, array $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function findApp($path)
    {
        foreach ($this->map as $handler) {
            if (preg_match($handler['path'], $path)) {
                return $this->container->get($handler['service']);
            }
        }

        throw new \RuntimeException('Failed to find application for path');
    }
}

<?php

namespace Veneer\CoreBundle\Service\Storage;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Veneer\CoreBundle\Service\Storage\Query\DirectoryQuery;
use Veneer\CoreBundle\Service\Storage\Query\FileQuery;

class System
{
    protected $container;
    protected $layers;

    public function __construct(ContainerInterface $container, array $layers)
    {
        $this->container = $container;
        $this->layers = $layers;
    }

    public function get($path, array $context = [])
    {
        $query = new FileQuery($this, $this->container, $this->layers, $path, $context, 'get');

        return $query->execute();
    }

    public function ls($path, array $context = [])
    {
        $query = new DirectoryQuery($this, $this->container, $this->layers, $path, $context, 'ls');

        return $query->execute();
    }
}

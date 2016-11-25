<?php

namespace Veneer\CoreBundle\Service\Storage\Query;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Veneer\CoreBundle\Service\Storage\System;

abstract class AbstractQuery
{
    protected $system;
    protected $container;
    protected $layers;
    protected $path;
    protected $context;

    protected $successful = null;
    protected $done = false;

    public function __construct(System $system, ContainerInterface $container, array $layers, $path, array $context, $method)
    {
        $this->system = $system;
        $this->container = $container;
        $this->layers = $layers;
        $this->path = $path;
        $this->context = $context;
        $this->method = $method;
    }

    public function execute()
    {
        $result = $this->next();

        if (!$this->isSuccessful()) {
            throw new \BadMethodCallException(sprintf('Unsuccessful %s operation on %s', $this->method, $this->path));
        }

        return $result;
    }

    public function next()
    {
        if (count($this->layers) == 0) {
            return $this->getResult();
        }

        $layer = $this->container->get(array_shift($this->layers));

        call_user_func([$layer, $this->method], $this);

        if ($this->isDone() || ($this->successful === false)) {
            return $this->getResult();
        }

        return $this->next();
    }

    public function done()
    {
        $this->done = true;

        return $this;
    }

    public function isDone()
    {
        return $this->done;
    }

    public function successful()
    {
        $this->successful = true;

        return $this;
    }

    public function isSuccessful()
    {
        return $this->successful;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getContextKey($key)
    {
        return isset($this->context[$key]) ? $this->context[$key] : null;
    }

    public function getSystem()
    {
        return $this->system;
    }

    abstract protected function getResult();
}

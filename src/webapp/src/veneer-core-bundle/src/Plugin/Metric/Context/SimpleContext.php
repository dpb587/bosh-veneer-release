<?php

namespace Veneer\CoreBundle\Plugin\Metric\Context;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SimpleContext implements ContextInterface
{
    use ContextTrait;

    private $container;
    private $map = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addService($alias, $id)
    {
        $this->map[$alias] = $id;

        return $this;
    }

    public function resolve($name)
    {
        if (!isset($this->map[$name])) {
            throw new \InvalidArgumentException();
        }

        $context = $this->container->get($this->map[$name]);
        $context->replaceContext($this->context);

        return $context;
    }
}

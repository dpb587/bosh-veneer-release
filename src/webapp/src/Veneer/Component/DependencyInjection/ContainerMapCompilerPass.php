<?php

namespace Veneer\Component\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ContainerMapCompilerPass implements CompilerPassInterface
{
    protected $service;
    protected $tag;
    protected $argument;

    public function __construct($service, $tag = null, $argument = 1)
    {
        $this->service = $service;
        $this->tag = $tag ?: $service;
        $this->argument = $argument;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->service)) {
            return;
        }

        $map = array();

        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" has not defined the "alias" property for tag "%s".', $id, $this->tag));
                }

                $map[$attribute['alias']] = $id;
            }
        }

        $container->getDefinition($this->service)->replaceArgument($this->argument, $map);
    }
}

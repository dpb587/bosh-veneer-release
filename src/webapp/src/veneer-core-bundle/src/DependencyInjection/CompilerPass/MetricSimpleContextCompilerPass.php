<?php

namespace Veneer\CoreBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MetricSimpleContextCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $map = array();

        foreach ($container->findTaggedServiceIds('veneer_core.plugin.metric.simple_context') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" has not defined the "alias" property for tag "veneer_core.plugin.metric.simple_context".', $id));
                } elseif (!isset($attribute['parent'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" has not defined the "parent" property for tag "veneer_core.plugin.metric.simple_context".', $id));
                }

                $map[$attribute['parent']][$attribute['alias']] = $id;
            }
        }

        foreach ($map as $service => $aliases) {
            if (!$container->hasDefinition($service)) {
                continue;
            }

            foreach ($aliases as $alias => $id) {
                $container->getDefinition($service)->addMethodCall(
                    'addService',
                    [
                        $alias,
                        $id,
                    ]
                );
            }
        }
    }
}

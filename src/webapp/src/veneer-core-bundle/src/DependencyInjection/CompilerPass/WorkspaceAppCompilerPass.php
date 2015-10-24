<?php

namespace Veneer\CoreBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class WorkspaceAppCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('veneer_core.workspace.app')) {
            return;
        }

        $map = new \SplPriorityQueue();

        foreach ($container->findTaggedServiceIds('veneer_core.workspace.app') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['path'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" is missing the "path" property for tag "veneer_core.workspace.app".', $id));
                }

                $map->insert(
                    [
                        'path' => $attribute['path'],
                        'service' => $id,
                    ],
                    isset($attribute['priority']) ? $attribute['priority'] : 0
                );
            }
        }

        $container->getDefinition('veneer_core.workspace.app')->replaceArgument(1, array_reverse(iterator_to_array($map)));
    }
}
<?php

namespace Veneer\WebBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RequestContextPluginCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('veneer_web.plugin.request_context.factory')) {
            return;
        }

        $map = array();

        foreach ($container->findTaggedServiceIds('veneer_web.request_context') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['context'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" is missing the "context" property for tag "veneer_web.request_context".', $id));
                }

                $map[$attribute['context']][] = $id;
            }
        }

        $container->getDefinition('veneer_web.plugin.request_context.factory')->replaceArgument(1, $map);
    }
}
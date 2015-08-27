<?php

namespace Veneer\WebBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TopicProviderPluginCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('veneer_web.plugin.topic_provider.factory')) {
            return;
        }

        $map = array();

        foreach ($container->findTaggedServiceIds('veneer_web.topic_provider') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['route'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" is missing the "route" property for tag "veneer_web.topic_provider".', $id));
                }

                $map[$attribute['route']][] = $id;
            }
        }

        $container->getDefinition('veneer_web.plugin.topic_provider.factory')->replaceArgument(1, $map);
    }
}
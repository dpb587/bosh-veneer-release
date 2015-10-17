<?php

namespace Veneer\CoreBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class LinkProviderPluginCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('veneer_web.plugin.link_provider.factory')) {
            return;
        }

        $map = array();

        foreach ($container->findTaggedServiceIds('veneer_web.link_provider') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['route'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" is missing the "route" property for tag "veneer_web.link_provider".', $id));
                }

                $map[$attribute['route']][] = $id;
            }
        }

        $container->getDefinition('veneer_web.plugin.link_provider.factory')->replaceArgument(1, $map);
    }
}
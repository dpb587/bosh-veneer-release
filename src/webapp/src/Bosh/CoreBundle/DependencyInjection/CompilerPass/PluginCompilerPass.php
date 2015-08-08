<?php

namespace Bosh\CoreBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class PluginCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bosh_core.plugin_factory')) {
            return;
        }

        $map = array();

        foreach ($container->findTaggedServiceIds('bosh_core.plugin') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['context'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" is missing the "context" property for tag "bosh_core.plugin".', $id));
                }

                $map[$id][] = $attribute['context'];
            }
        }

        $container->getDefinition('bosh_core.plugin_factory')->replaceArgument(1, $map);
    }
}
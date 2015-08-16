<?php

namespace Veneer\BoshBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class PluginCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('veneer_bosh.plugin_factory')) {
            return;
        }

        $map = array();

        foreach ($container->findTaggedServiceIds('veneer_bosh.plugin') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['context'])) {
                    throw new \InvalidArgumentException(sprintf('The service "%s" is missing the "context" property for tag "veneer_bosh.plugin".', $id));
                }

                $map[$id][] = $attribute['context'];
            }
        }

        $container->getDefinition('veneer_bosh.plugin_factory')->replaceArgument(1, $map);
    }
}
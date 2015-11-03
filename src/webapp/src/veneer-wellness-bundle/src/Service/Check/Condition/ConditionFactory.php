<?php

namespace Veneer\WellnessBundle\Service\Check\Condition;

use Veneer\CoreBundle\DependencyInjection\ContainerMap;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

class ConditionFactory extends ContainerMap
{
    public function compileConfig($condition, array $conditionConfig)
    {
        $definition = new TreeBuilder();
        $definitionRoot = $definition->root('source');
        $this->get($condition)->getConfiguration($definitionRoot);

        $processor = new Processor();

        return $processor->process(
            $definition->buildTree(),
            [
                $conditionConfig,
            ]
        );
    }
}

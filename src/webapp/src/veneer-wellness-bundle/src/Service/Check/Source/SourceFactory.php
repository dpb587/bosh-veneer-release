<?php

namespace Veneer\WellnessBundle\Service\Check\Source;

use Veneer\CoreBundle\DependencyInjection\ContainerMap;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

class SourceFactory extends ContainerMap
{
    public function compileConfig($source, array $sourceConfig)
    {
        $definition = new TreeBuilder();
        $definitionRoot = $definition->root('source');
        $this->get($source)->getConfiguration($definitionRoot);

        $processor = new Processor();

        return $processor->process(
            $definition->buildTree(),
            [
                $sourceConfig,
            ]
        );
    }
}

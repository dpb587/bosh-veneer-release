<?php

namespace Veneer\WellnessBundle\Service\Check\Source;

use Veneer\WellnessBundle\Service\Check\Check;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

interface SourceInterface
{
    public function getConfiguration(NodeDefinition $tree);
    public function load(Check $check);
}

<?php

namespace Veneer\WellnessBundle\Service\Check\Condition;

use Veneer\WellnessBundle\Service\Check\Check;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

interface ConditionInterface
{
    public function getConfiguration(NodeDefinition $tree);
    public function evaluate(Check $check);
}

<?php

namespace Veneer\WellnessBundle\Service\Check\Action;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Veneer\WellnessBundle\Service\Check\Check;

interface ActionInterface
{
    public function getConfiguration(NodeDefinition $tree);
    public function execute(Check $check);
}

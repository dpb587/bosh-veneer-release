<?php

namespace Veneer\CoreBundle\Service\Workspace\Environment;

interface EnvironmentInterface
{
    public function load(EnvironmentContext $env, $path);
}

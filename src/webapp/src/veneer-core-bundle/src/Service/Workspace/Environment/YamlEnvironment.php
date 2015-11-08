<?php

namespace Veneer\CoreBundle\Service\Workspace\Environment;

use Symfony\Component\Yaml\Yaml;

class YamlEnvironment implements EnvironmentInterface
{
    public function load(EnvironmentContext $env, $path)
    {
        return Yaml::parse($env->getCheckout()->get($env->resolveRelativePath($path . '.yml')));
    }
}

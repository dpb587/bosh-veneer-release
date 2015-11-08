<?php

namespace Veneer\CoreBundle\Service\Workspace\Environment;

class FileEnvironment implements EnvironmentInterface
{
    public function load(EnvironmentContext $env, $path)
    {
        return $env->getCheckout()->get($env->resolveRelativePath($path));
    }
}

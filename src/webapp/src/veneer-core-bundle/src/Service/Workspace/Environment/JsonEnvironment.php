<?php

namespace Veneer\CoreBundle\Service\Workspace\Environment;

class JsonEnvironment implements EnvironmentInterface
{
    public function load(EnvironmentContext $env, $path)
    {
        return json_decode($env->getCheckout()->get($env->resolveRelativePath($path . '.json')), true);
    }
}

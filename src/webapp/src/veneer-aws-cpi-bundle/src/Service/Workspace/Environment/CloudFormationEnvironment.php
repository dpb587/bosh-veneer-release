<?php

namespace Veneer\AwsCpiBundle\Service\Workspace\Environment;

use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentInterface;
use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentContext;

class CloudFormationEnvironment implements EnvironmentInterface
{
    public function load(EnvironmentContext $env, $path)
    {
        // backcompat
        $path = '../' . ((null === $path) ? basename(dirname($env->getContextPath())) : $path);

        return json_decode($env->getCheckout()->get($env->resolveRelativePath($path . '/cloudformation-state.json')), true);
    }
}

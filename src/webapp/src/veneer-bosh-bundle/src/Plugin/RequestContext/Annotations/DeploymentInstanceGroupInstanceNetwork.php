<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class DeploymentInstanceGroupInstanceNetwork extends DeploymentInstanceGroupInstance
{
    final public function getNetworkAttribute()
    {
        return 'network';
    }
}

<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class DeploymentInstanceGroupInstance extends DeploymentInstanceGroup
{
    final public function getInstanceAttribute()
    {
        return 'instance';
    }
}

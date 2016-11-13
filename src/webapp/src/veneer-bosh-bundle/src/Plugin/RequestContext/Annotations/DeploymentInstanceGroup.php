<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class DeploymentInstanceGroup extends Deployment
{
    final public function getInstanceGroupAttribute()
    {
        return 'instance_group';
    }
}

<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class DeploymentInstanceGroupInstancePersistentDisk extends DeploymentInstanceGroupInstance
{
    final public function getPersistentDiskAttribute()
    {
        return 'persistent_disk';
    }
}

<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

use Veneer\CoreBundle\Plugin\RequestContext\Annotation;

/**
 * @Annotation
 */
class Deployment extends Annotation
{
    final public function getDeploymentAttribute()
    {
        return 'deployment';
    }
}

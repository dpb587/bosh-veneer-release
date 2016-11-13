<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

use Veneer\CoreBundle\Plugin\RequestContext\Annotation;

/**
 * @Annotation
 */
class CloudConfig extends Annotation
{
    final public function getCloudConfigAttribute()
    {
        return 'cloudconfig';
    }
}

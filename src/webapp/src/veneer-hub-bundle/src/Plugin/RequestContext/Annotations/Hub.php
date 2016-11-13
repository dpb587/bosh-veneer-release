<?php

namespace Veneer\HubBundle\Plugin\RequestContext\Annotations;

use Veneer\CoreBundle\Plugin\RequestContext\Annotation;

/**
 * @Annotation
 */
class Hub extends Annotation
{
    final public function getHubAttribute()
    {
        return 'hub';
    }
}

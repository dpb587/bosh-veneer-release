<?php

namespace Veneer\HubBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class HubRelease extends Hub
{
    final public function getReleaseAttribute()
    {
        return 'release';
    }
}

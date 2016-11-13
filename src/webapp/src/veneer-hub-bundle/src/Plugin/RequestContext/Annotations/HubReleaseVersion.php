<?php

namespace Veneer\HubBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class HubReleaseVersion extends HubRelease
{
    final public function getVersionAttribute()
    {
        return 'version';
    }
}

<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class ReleaseVersion extends Release
{
    final public function getVersionAttribute()
    {
        return 'version';
    }
}

<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class ReleaseJob extends Release
{
    final public function getJobAttribute()
    {
        return 'job';
    }

    final public function getVersionAttribute()
    {
        return 'version';
    }
}

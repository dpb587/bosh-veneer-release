<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class ReleasePackage extends Release
{
    final public function getPackageAttribute()
    {
        return 'package';
    }

    final public function getVersionAttribute()
    {
        return 'version';
    }
}

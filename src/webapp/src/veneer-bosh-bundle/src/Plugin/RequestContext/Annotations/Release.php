<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

use Veneer\CoreBundle\Plugin\RequestContext\Annotation;

/**
 * @Annotation
 */
class Release extends Annotation
{
    final public function getReleaseAttribute()
    {
        return 'release';
    }
}

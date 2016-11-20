<?php

namespace Veneer\CoreBundle\Plugin\RequestContext\Annotations;

use Veneer\CoreBundle\Plugin\RequestContext\Annotation;

/**
 * @Annotation
 */
class AppPath extends Annotation
{
    public $name;

    final public function getPathAttribute()
    {
        return 'file';
    }
}

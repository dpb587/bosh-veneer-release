<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

use Veneer\CoreBundle\Plugin\RequestContext\Annotation;

/**
 * @Annotation
 */
class Task extends Annotation
{
    final public function getTaskAttribute()
    {
        return 'task';
    }
}

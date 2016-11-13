<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

use Veneer\CoreBundle\Plugin\RequestContext\Annotation;

/**
 * @Annotation
 */
class Event extends Annotation
{
    final public function getEventAttribute()
    {
        return 'event';
    }
}

<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

use Veneer\CoreBundle\Plugin\RequestContext\Annotation;

/**
 * @Annotation
 */
class Stemcell extends Annotation
{
    final public function getStemcellAttribute()
    {
        return 'stemcell';
    }
}

<?php

namespace Veneer\BoshBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class StemcellVersion extends Stemcell
{
    final public function getVersionAttribute()
    {
        return 'version';
    }
}

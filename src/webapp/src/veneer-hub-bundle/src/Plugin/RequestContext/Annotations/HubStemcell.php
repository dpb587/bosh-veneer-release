<?php

namespace Veneer\HubBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class HubStemcell extends Hub
{
    final public function getStemcellAttribute()
    {
        return 'stemcell';
    }
}

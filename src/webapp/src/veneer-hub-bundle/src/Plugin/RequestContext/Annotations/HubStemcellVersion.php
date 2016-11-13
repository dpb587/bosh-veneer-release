<?php

namespace Veneer\HubBundle\Plugin\RequestContext\Annotations;

/**
 * @Annotation
 */
class HubStemcellVersion extends HubStemcell
{
    final public function getVersionAttribute()
    {
        return 'version';
    }
}

<?php

namespace Veneer\BoshBundle\Service\Cpi;

use Veneer\CoreBundle\DependencyInjection\ContainerMap;

class CpiFactory extends ContainerMap
{
    public function lookup(array $context = [])
    {
        return $this->get($this->allKeys()[0]);
    }
}

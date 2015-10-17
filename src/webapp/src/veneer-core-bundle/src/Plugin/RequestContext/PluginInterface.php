<?php

namespace Veneer\CoreBundle\Plugin\RequestContext;

use Symfony\Component\HttpFoundation\Request;

interface PluginInterface
{
    public function applyContext(Request $request, $context);
}

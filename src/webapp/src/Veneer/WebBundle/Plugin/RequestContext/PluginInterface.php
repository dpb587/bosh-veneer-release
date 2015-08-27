<?php

namespace Veneer\WebBundle\Plugin\RequestContext;

use Symfony\Component\HttpFoundation\Request;

interface PluginInterface
{
    public function applyContext(Request $request, $context);
}

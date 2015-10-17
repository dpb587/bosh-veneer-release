<?php

namespace Veneer\CoreBundle\Plugin\LinkProvider;

use Symfony\Component\HttpFoundation\Request;

interface PluginInterface
{
    public function getLinks(Request $request, $route);
}

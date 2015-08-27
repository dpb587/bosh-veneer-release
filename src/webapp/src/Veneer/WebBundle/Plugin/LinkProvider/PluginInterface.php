<?php

namespace Veneer\WebBundle\Plugin\LinkProvider;

use Symfony\Component\HttpFoundation\Request;

interface PluginInterface
{
    public function getLinks(Request $request, $route);
}

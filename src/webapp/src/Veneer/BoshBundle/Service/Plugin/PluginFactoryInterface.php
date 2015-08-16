<?php

namespace Veneer\BoshBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;

interface PluginFactoryInterface
{
    public function getContext(Request $request, $contextName);
}

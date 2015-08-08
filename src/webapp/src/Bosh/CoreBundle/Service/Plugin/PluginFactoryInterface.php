<?php

namespace Bosh\CoreBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;

interface PluginFactoryInterface
{
    public function getContext(Request $request, $contextName);
}

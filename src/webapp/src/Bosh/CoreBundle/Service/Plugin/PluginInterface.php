<?php

namespace Bosh\CoreBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;

interface PluginInterface
{
    public function getContext(Request $request, $contextName);
    public function getEndpoints($contextName, array $context = []);
    #public function getUserPrimaryLinks($scope, array $context = []);
    #public function getUserReferenceLinks($scope, array $context = []);
}

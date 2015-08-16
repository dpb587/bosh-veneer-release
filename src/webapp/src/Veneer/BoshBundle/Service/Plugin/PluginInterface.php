<?php

namespace Veneer\BoshBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;

interface PluginInterface
{
    const USER_SECONDARY_TOPIC_CONFIG = 'config';
    const USER_SECONDARY_TOPIC_RESOURCES = 'resources';
    const USER_SECONDARY_TOPIC_PERFORMANCE = 'performance';
    const USER_SECONDARY_TOPIC_CPI = 'cpi';
    const USER_SECONDARY_TOPIC_DOCUMENTATION = 'documentation';
    const USER_SECONDARY_TOPIC_OTHER = 'other';

    public function getContext(Request $request, $contextName);
    public function getEndpoints($contextName, array $context = []);
    public function getUserPrimaryLinks($contextName, array $context = []);
    public function getUserReferenceLinks($scope, array $context = []);
}

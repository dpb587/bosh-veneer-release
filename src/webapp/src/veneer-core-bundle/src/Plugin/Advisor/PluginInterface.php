<?php

namespace Veneer\CoreBundle\Plugin\Advisor;

use Symfony\Component\HttpFoundation\Request;

interface PluginInterface
{
    const TOPIC_CPI = 'cpi';
    const TOPIC_MONEY = 'money';
    const TOPIC_PERFORMANCE = 'performance';

    public function getTitle(Request $request, $route);
}

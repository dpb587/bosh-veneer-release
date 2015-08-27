<?php

namespace Veneer\WebBundle\Plugin\RequestContext;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Factory
{
    protected $container;
    protected $contextMap;

    public function __construct(ContainerInterface $container, array $contextMap = [])
    {
        $this->container = $container;
        $this->contextMap = $contextMap;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_veneer_web_context')) {
            return;
        }

        $contextList = (array) $request->attributes->get('_veneer_web_context');

        foreach ($contextList as $context) {
            if (!isset($this->contextMap[$context])) {
                continue;
            }

            foreach ($this->contextMap[$context] as $service) {
                $this->container->get($service)->applyContext($request, $context);
            }
        }
    }
}

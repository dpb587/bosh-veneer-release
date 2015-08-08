<?php

namespace Bosh\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Bosh\CoreBundle\Service\Plugin\PluginFactoryInterface;

class ObjectContextRequestListener
{
    protected $pluginFactory;

    public function __construct(PluginFactoryInterface $pluginFactory)
    {
        $this->pluginFactory = $pluginFactory;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_bosh_web_object_context')) {
            return;
        }

        $request->attributes->set(
            '_context',
            $this->pluginFactory->getContext(
                $request,
                $request->attributes->get('_bosh_web_object_context')
            )
        );
    }
}

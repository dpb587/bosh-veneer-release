<?php

namespace Veneer\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class JsonResponseListener
{
    protected $debug;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        if (($response instanceof JsonResponse) && ($this->debug || $request->query->has('pretty'))) {
            $response->setContent(json_encode(json_decode($response->getContent(), true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}

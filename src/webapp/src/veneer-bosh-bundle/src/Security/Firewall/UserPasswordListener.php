<?php

namespace Veneer\BoshBundle\Security\Firewall;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Veneer\BoshBundle\Security\Core\Authentication\Token\BasicToken;
use Veneer\CoreBundle\Service\EncryptionService;

class UserPasswordListener
{
    protected $securityContext;
    protected $encryptionService;

    public function __construct(SecurityContextInterface $securityContext, EncryptionService $encryptionService)
    {
        $this->securityContext = $securityContext;
        $this->encryptionService = $encryptionService;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $token = $this->securityContext->getToken();

        if (!$token || !$token instanceof BasicToken || ('form' != $token->getAttribute('auth.method'))) {
            return;
        }

        $token->getUser()->setCredentials(
            $this->encryptionService->decrypt($event->getRequest()->cookies->get('_bosh_password'))
        );
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        } elseif (!$event->getRequest()->attributes->has(DefaultFirewall::RESPONSE_LISTENER_ATTRIBUTE)) {
            return;
        }

        $action = $event->getRequest()->attributes->get(DefaultFirewall::RESPONSE_LISTENER_ATTRIBUTE);

        $response = $event->getResponse();

        if ('set' == $action) {
            $response->headers->setCookie(new Cookie(
                '_bosh_password',
                $this->encryptionService->encrypt($this->securityContext->getToken()->getUser()->getCredentials()),
                0,
                '/'
            ));
        }
    }
}
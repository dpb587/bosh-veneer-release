<?php

namespace Veneer\BoshBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Veneer\BoshBundle\Security\Core\Authentication\Token\BasicToken;
use Veneer\BoshBundle\Security\Core\Authentication\Token\UaaToken;

class DefaultFirewall extends AbstractAuthenticationListener
{
    const RESPONSE_LISTENER_ATTRIBUTE = '_veneer_bosh_default_firewall';

    protected function requiresAuthentication(Request $request)
    {
        return ('POST' == $request->getMethod()) && (preg_match('#^/auth/via/(basic|uaa)$#', $request->getPathInfo()))
            || $request->headers->has('authorization');
    }

    protected function attemptAuthentication(Request $request)
    {
        if (preg_match('#^/auth/via/(basic|uaa)$#', $request->getPathInfo(), $method)) {
            if ('basic' == $method[1]) {
                $basic = $request->request->get('basic');

                if ($request->request->has('basic')) {
                } else {
                    throw new HttpException(400);
                }

                $token = new BasicToken(
                    (isset($basic['username']) ? $basic['username'] : ''),
                    (isset($basic['password']) ? $basic['password'] : null),
                    $this->providerKey
                );
            } else {
                throw new \LogicException('Not really sure what UAA looks like...');
            }

            $token->setAttribute('auth.method', 'form');
        } elseif ($request->headers->has('authorization')) {
            if (null !== $request->getUser()) {
                // basic authentication used
                $token = new SimpleToken(
                    $request->getUser(),
                    $request->getPassword(),
                    $this->providerKey
                );
            } else {
                $token = new UaaToken(
                    'uaa',
                    $request->headers->get('authorization'),
                    $this->providerKey
                );
            }

            $token->setAttribute('auth.method', 'header');
        } else {
            throw new \LogicException();
        }

        $authenticatedToken = $this->authenticationManager->authenticate($token);

        if (($authenticatedToken instanceof BasicToken) && ('form' == $token->getAttribute('auth.method'))) {
            // they logged in via web form
            // we still might need to use their password, so set an attribute
            // that we can check for in UserPasswordListener
            $request->attributes->set(static::RESPONSE_LISTENER_ATTRIBUTE, 'set');
        }

        return $authenticatedToken;
    }
}
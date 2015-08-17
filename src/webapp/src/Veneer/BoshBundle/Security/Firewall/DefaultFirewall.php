<?php

namespace Veneer\BoshBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

class DefaultFirewall extends AbstractAuthenticationListener
{
    protected function requiresAuthentication(Request $request)
    {
        return preg_match('#^/auth/via/builtin$#', $request->getPathInfo())
            || $request->headers->has('authorization');
    }

    protected function attemptAuthentication(Request $request)
    {
        if (preg_match('#^/auth/via/simple$#', $request->getPathInfo())) {
            if ($request->request->has('simple')) {
                $simple = $request->request->get('simple');
            } else {
                throw new HttpException(400);
            }

            $token = new UsernamePasswordToken(
                (isset($simple['username']) ? $simple['username'] : ''),
                (isset($simple['password']) ? $simple['password'] : null),
                $this->providerKey
            );

            $token->setAttribute('auth.source', 'form');
        } elseif ($request->headers->has('authorization')) {
            if (null !== $request->getUser()) {
                $token = new UsernamePasswordToken(
                    $request->getUser(),
                    $request->getPassword(),
                    $this->providerKey
                );

                $token->setAttribute('auth.source', 'header');
            } else {
                // @todo
                $token = new PreAuthenticatedToken(
                    'unknown',
                    $request->headers->get('authorization'),
                    $this->providerKey
                );
            }
        } else {
            throw new \LogicException();
        }

        return $this->authenticationManager->authenticate($token);
    }
}
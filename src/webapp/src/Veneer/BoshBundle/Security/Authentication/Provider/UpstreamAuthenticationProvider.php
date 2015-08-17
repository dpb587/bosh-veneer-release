<?php

namespace Veneer\BoshBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Veneer\Component\BoshApi\Client;

class UpstreamAuthenticationProvider implements AuthenticationProviderInterface
{
    protected $providerKey;
    protected $boshApi;

    public function __construct($providerKey, Client $boshApi)
    {
        $this->providerKey = $providerKey;
        $this->boshApi = $boshApi;
    }

    public function authenticate(TokenInterface $token)
    {
        $authToken = $this->authenticateToken($token);

        if ($authToken instanceof TokenInterface) {
            return $authToken;
        }

        throw new AuthenticationException('Failed to authenticate token.');
    }

    protected function authenticateToken(TokenInterface $token)
    {
    }

    public function supports(TokenInterface $token)
    {
        return $this->simpleAuthenticator->supportsToken($token, $this->providerKey);
    }
}
<?php

namespace Veneer\BoshBundle\Security\Core\Authentication\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Veneer\Component\BoshApi\Client;
use Veneer\Component\BoshApi\SecurityTokenAuthentication;
use Veneer\BoshBundle\Security\Core\Authentication\Token\AbstractToken;
use Veneer\BoshBundle\Security\User\User;

class BoshDirectorProvider implements AuthenticationProviderInterface
{
    protected $providerKey;
    protected $boshApi;
    protected $cacheDuration;

    public function __construct($providerKey, array $boshApiOptions, $cacheDuration)
    {
        $this->providerKey = $providerKey;
        $this->boshApiOptions = $boshApiOptions;
        $this->cacheDuration = $cacheDuration;
    }

    public function authenticate(TokenInterface $token)
    {
        $boshApi = new Client($this->boshApiOptions, $token);

        try {
            $info = $boshApi->getInfo();

            $user = new User(
                $info['user'],
                $token->getCredentials(),
                [
                    'ROLE_USER',
                ]
            );

            $class = get_class($token);
            $authenticatedToken = new $class(
                $user,
                $token->getCredentials(),
                $this->providerKey,
                $user->getRoles()
            );

            $authenticatedToken->setAttributes($token->getAttributes());
            $authenticatedToken->setAttribute('auth.expires', new \DateTime('+' . $this->cacheDuration . ' seconds'));

            return $authenticatedToken;
        } catch (\Exception $e) {
            // @todo wrapped
        }

        throw new AuthenticationException('BOSH authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof AbstractToken;
    }
}

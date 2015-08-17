<?php

namespace Veneer\BoshBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

class CachedUser implements UserInterface
{
    protected $source;
    protected $expiration;
    protected $profile;
    protected $roles;

    public function __construct($source, \DateTime $expiration, array $profile = [], array $roles = array())
    {
        $this->source = $source;
        $this->expiration = $expiration;
        $this->profile = $profile;
        $this->roles = $roles;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function getProfile()
    {
        return array_merge(
            $this->profile,
            [
                '_source' => $this->source,
            ]
        );
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return isset($this->profile['password']) ? $this->profile['password'] : null;
    }

    public function getSalt()
    {
        return isset($this->profile['salt']) ? $this->profile['salt'] : null;
    }

    public function getUsername()
    {
        return $this->profile['username'];
    }

    public function eraseCredentials()
    {
        unset(
            $this->profile['password'],
            $this->profile['salt']
        );
    }

    public function equals(UserInterface $user)
    {
        return __CLASS__ == get_class($user) && $this->getSource() == $user->getSource() && $this->getUsername() == $user->getUsername();
    }

    public function __sleep()
    {
        return array('source', 'expiration', 'profile', 'roles');
    }
}

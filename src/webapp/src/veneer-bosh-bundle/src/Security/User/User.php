<?php

namespace Veneer\BoshBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    protected $username;
    protected $credentials;
    protected $roles;

    public function __construct($username, $credentials, array $roles = array())
    {
        $this->username = $username;
        $this->credentials = $credentials;
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return null;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

    public function eraseCredentials()
    {
        // we don't actually erase credentials from the user
        // because we might want to impersonate them with an
        // upstream api request

        // instead, we avoid serializing their credentials
    }

    public function equals(UserInterface $user)
    {
        return __CLASS__ == get_class($user) && $this->getUsername() == $user->getUsername();
    }

    public function __sleep()
    {
        return array('username', 'roles');
    }
}

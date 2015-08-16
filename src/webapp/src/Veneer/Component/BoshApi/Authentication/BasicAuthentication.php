<?php

namespace Veneer\Component\BoshApi\Authentication;

class BasicAuthentication implements AuthenticationInterface
{
    protected $username;
    protected $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getAuthorizationHeader()
    {
        return 'Basic ' . base64_encode($this->username . ':' . $this->password);
    }
}

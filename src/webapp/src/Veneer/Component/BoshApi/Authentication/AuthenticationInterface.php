<?php

namespace Veneer\Component\BoshApi\Authentication;

interface AuthenticationInterface
{
    public function getUsername();
    public function getAuthorizationHeader();
}

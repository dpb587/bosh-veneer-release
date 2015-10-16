<?php

namespace Veneer\AwsCpiBundle\Service\Api;

class FormHelper
{
    protected $ec2;

    public function __construct($ec2)
    {
        $this->ec2 = $ec2;
    }

    public function lookupVpcSubnets($search = null)
    {

    }
}

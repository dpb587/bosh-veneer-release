<?php


namespace Veneer\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Cidr extends Constraint
{
    public $message = 'This is not a valid CIDR.';
    public $messageIp = 'This is not a valid IP.';
    public $messageNetmask = 'This is not a valid CIDR network mask.';
}

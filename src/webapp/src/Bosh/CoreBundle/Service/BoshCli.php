<?php

namespace Bosh\CoreBundle\Service;

use Symfony\Component\Process\Process;

class BoshCli
{
    protected $executable;
    
    public function __construct($executable = 'bosh')
    {
        $this->executable = $executable;
    }
    
    
}
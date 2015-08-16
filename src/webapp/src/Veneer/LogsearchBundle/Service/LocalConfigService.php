<?php

namespace Veneer\LogsearchBundle\Service;

class LocalConfigService
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }
}

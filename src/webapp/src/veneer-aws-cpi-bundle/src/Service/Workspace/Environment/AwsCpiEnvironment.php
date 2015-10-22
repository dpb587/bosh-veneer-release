<?php

namespace Veneer\AwsCpiBundle\Service\Workspace\Environment;

use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentInterface;
use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentContext;

class AwsCpiEnvironment implements EnvironmentInterface
{
    protected $region;

    public function __construct($region)
    {
        $this->region = $region;
    }

    public function load(EnvironmentContext $env, $path)
    {
        if (null !== $path) {
            throw new \InvalidArgumentException('Environment does not accept a path');
        }

        return [
            'region' => $this->region,
        ];
    }
}

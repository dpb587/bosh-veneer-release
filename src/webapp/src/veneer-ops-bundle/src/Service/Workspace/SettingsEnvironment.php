<?php

namespace Veneer\OpsBundle\Service\Workspace;

use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentInterface;
use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentContext;

class SettingsEnvironment implements EnvironmentInterface
{
    protected $repo;

    public function __construct(GitRepository $repo)
    {
        $this->repo = $repo;
    }

    public function load(EnvironmentContext $env, $path)
    {
        $contextPath = $env->getContextPath();

        if (null === $path) {
            $file = dirname($contextPath) . '/settings.yml';
        } else {
            throw new \LogicException('@todo resolve paths');
        }
        
        $yaml = Yaml::parse($this->repo->showFile($file));

        return $yaml;
    }
}

<?php

namespace Veneer\BoshBundle\Service\Workspace\Environment;

use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentInterface;
use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentContext;

class BoshEnvironment implements EnvironmentInterface
{
    protected $em;
    protected $directorName;

    public function __construct(EntityManager $em, $directorName)
    {
        $this->em = $em;
        $this->directorName = $directorName;
    }

    public function load(EnvironmentContext $env, $path)
    {
        if (null !== $path) {
            throw new \InvalidArgumentException('Environment does not accept a path');
        }

        $attributes = [
            'name' => $this->directorName,
        ];

        foreach ($this->em->getRepository('VeneerBoshBundle:DirectorAttributes')->findAll() as $attributeRaw) {
            $attributes[$attributeRaw['name']] = $attributeRaw['value'];
        }

        return $attributes;
    }
}

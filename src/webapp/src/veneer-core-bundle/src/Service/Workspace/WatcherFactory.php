<?php

namespace Veneer\CoreBundle\Service\Workspace;

use Symfony\Component\DependencyInjection\ContainerInterface;

class WatcherFactory
{
    protected $container;
    protected $map;

    public function __construct(ContainerInterface $container, array $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function handleChangeset($branch, Changeset $changeset)
    {
        $handles = [];

        foreach ($changeset as $path => $change) {
            foreach ($this->map as $handler) {
                if (preg_match($handler['path'], $path)) {
                    $handles[] = [
                        'path' => $path,
                        'service' => $handler['service'],
                        'method' => $handler['method'],
                    ];
                }
            }
        }

        foreach ($handles as $handle) {
            call_user_func_array(
                [
                    $this->container->get($handle['service']),
                    $handle['method'],
                ],
                [
                    $branch,
                    $changeset,
                    $handle['path'],
                ]
            );
        }
    }
}

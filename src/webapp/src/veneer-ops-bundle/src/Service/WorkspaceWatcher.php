<?php

namespace Veneer\OpsBundle\Service;

use Veneer\CoreBundle\Service\Workspace\Event\BlobEvent;

class WorkspaceWatcher
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onDeploymentManifest(BlobEvent $event)
    {
        
    }
}

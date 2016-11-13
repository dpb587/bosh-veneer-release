<?php

namespace Veneer\WardenCpiBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\DeploymentInstanceGroupInstancePersistentDisk
 */
class CoreDeploymentInstanceGroupInstancePersistentDiskController extends AbstractController
{
    public function cpiAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerWardenCpiBundle:CoreDeploymentInstanceGroupInstancePersistentDisk:cpi.html.twig',
            [
                'properties' => $_bosh['persistent_disk']['cloudPropertiesJsonAsArray'],
            ]
        );
    }
}

<?php

namespace Veneer\WardenCpiBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;

class CoreDeploymentInstanceGroupInstancePersistentDiskController extends AbstractController
{
    public function cpiAction(array $_bosh)
    {
        return $this->renderApi(
            'VeneerWardenCpiBundle:CoreDeploymentInstanceGroupInstancePersistentDisk:cpi.html.twig',
            [
                'properties' => $_bosh['persistent_disk']['cloudPropertiesJsonAsArray'],
            ]
        );
    }
}

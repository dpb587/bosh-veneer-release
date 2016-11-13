<?php

namespace Veneer\AwsCpiBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class CoreDeploymentInstanceGroupInstancePersistentDiskController extends AbstractController
{
    public function cpiAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerAwsCpiBundle:CoreDeploymentInstanceGroupInstancePersistentDisk:cpi.html.twig',
            [
                'properties' => $_bosh['persistent_disk']['cloudPropertiesJsonAsArray'],
            ]
        );
    }
}

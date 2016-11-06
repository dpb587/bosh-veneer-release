<?php

namespace Veneer\AwsCpiBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;

class CoreDeploymentVmResourcePoolController extends AbstractController
{
    public function cpiAction(array $_bosh)
    {
        return $this->renderApi(
            'VeneerAwsCpiBundle:CoreDeploymentVmResourcePool:cpi.html.twig',
            [
                'properties' => $_bosh['vm']['applySpecJsonAsArray']['resource_pool']['cloud_properties'],
            ]
        );
    }
}

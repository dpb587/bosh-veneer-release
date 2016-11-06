<?php

namespace Veneer\AwsCpiBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;

class CoreDeploymentInstanceGroupInstanceNetworkController extends AbstractController
{
    public function cpiAction(array $_bosh)
    {
        return $this->renderApi(
            'VeneerAwsCpiBundle:CoreDeploymentInstanceGroupInstanceNetwork:cpi.html.twig',
            [
                'properties' => $_bosh['network']['cloud_properties'],
            ]
        );
    }
}

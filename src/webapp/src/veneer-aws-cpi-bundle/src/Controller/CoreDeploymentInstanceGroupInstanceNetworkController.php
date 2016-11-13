<?php

namespace Veneer\AwsCpiBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class CoreDeploymentInstanceGroupInstanceNetworkController extends AbstractController
{
    public function cpiAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerAwsCpiBundle:CoreDeploymentInstanceGroupInstanceNetwork:cpi.html.twig',
            [
                'properties' => $_bosh['network']['cloud_properties'],
            ]
        );
    }
}

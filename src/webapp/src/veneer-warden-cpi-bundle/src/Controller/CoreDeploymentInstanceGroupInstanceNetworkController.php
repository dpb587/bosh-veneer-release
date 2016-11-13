<?php

namespace Veneer\WardenCpiBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\DeploymentInstanceGroupInstanceNetwork
 */
class CoreDeploymentInstanceGroupInstanceNetworkController extends AbstractController
{
    public function cpiAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerWardenCpiBundle:CoreDeploymentInstanceGroupInstanceNetwork:cpi.html.twig',
            [
                'properties' => $_bosh['network']['cloud_properties'],
            ]
        );
    }
}

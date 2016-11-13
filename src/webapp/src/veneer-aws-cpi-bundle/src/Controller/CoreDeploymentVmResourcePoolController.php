<?php

namespace Veneer\AwsCpiBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class CoreDeploymentVmResourcePoolController extends AbstractController
{
    public function cpiAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerAwsCpiBundle:CoreDeploymentVmResourcePool:cpi.html.twig',
            [
                'properties' => $_bosh['vm']['applySpecJsonAsArray']['resource_pool']['cloud_properties'],
            ]
        );
    }
}

<?php

namespace Veneer\BoshBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentInstanceGroupInstanceNetworkController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentInstanceGroupInstanceNetworkALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['network']['name'],
                [
                    'veneer_bosh_deployment_instancegroup_instance_network_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                        'instance' => $_bosh['instance']['uuid'],
                        'network' => $_bosh['network']['name'],
                    ],
                ]
            );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstanceNetwork:summary.html.twig',
            [
                'data' => $_bosh['network'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function cpiAction(Request $request, $_bosh)
    {
        return $this->forward(
            $this->container->get('veneer_bosh.cpi')->lookup()->getControllerAction('CoreDeploymentInstanceGroupInstanceNetwork'),
            [
                '_bosh' => $_bosh,
                '_route' => $request->attributes->get('_route'),
                '_route_params' => $request->attributes->get('_route_params'),
            ],
            $request->query->all()
        );
    }
}

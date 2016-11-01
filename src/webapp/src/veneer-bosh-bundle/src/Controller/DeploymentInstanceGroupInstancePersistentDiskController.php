<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentInstanceGroupInstancePersistentDiskController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentInstanceGroupInstancePersistentDiskALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['persistent_disk']['size'] . ' MB',
                [
                    'veneer_bosh_deployment_instancegroup_instance_persistentdisk_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                        'instance' => $_bosh['instance']['uuid'],
                        'persistent_disk' => $_bosh['persistent_disk']['id'],
                    ],
                ],
                [
                    'expanded' => true,
                ]
            );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstancePersistentDisk:summary.html.twig',
            [
                'data' => $_bosh['persistent_disk'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
    
    public function cpiAction(Request $request, $_bosh)
    {
        return $this->forward(
            'VeneerAwsCpiBundle:CoreDeploymentInstanceGroupInstancePersistentDisk:cpi',
            [
                '_bosh' => $_bosh,
                '_route' => $request->attributes->get('_route'),
                '_route_params' => $request->attributes->get('_route_params'),
            ],
            $request->query->all()
        );
    }
}

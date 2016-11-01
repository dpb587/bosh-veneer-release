<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentInstanceGroupController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentInstanceGroupALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['instance_group']['job'],
                [
                    'veneer_bosh_deployment_instancegroup_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                    ],
                ],
                [
                    'expanded' => true,
                ]
            )
        ;
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroup:summary.html.twig',
            [
                'data' => $_bosh['instance_group'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

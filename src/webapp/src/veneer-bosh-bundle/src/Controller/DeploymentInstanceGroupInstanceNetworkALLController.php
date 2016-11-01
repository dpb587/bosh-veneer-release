<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentInstanceGroupInstanceNetworkALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentInstanceGroupInstanceController::defNav($nav, $_bosh)
            ->add(
                'network',
                [
                    'veneer_bosh_deployment_instancegroup_instance_networkALL_index' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                        'instance' => $_bosh['instance']['uuid'],
                    ],
                ],
                [
                    'fontawesome' => 'exchange',
                ]
            );
    }

    public function indexAction($_bosh)
    {
        $results = $_bosh['instance']['specJsonAsArray']['networks'];
        
        foreach ($results as $k => $v) {
            $results[$k]['name'] = $k;
        }

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstanceNetworkALL:index.html.twig',
            [
                'results' => array_values($results),
            ]
        );
    }
}

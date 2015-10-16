<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class DeploymentInstanceController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentController::defNav($nav, $_bosh)
            ->add(
                $_bosh['instance']['job'] . '/' . $_bosh['instance']['index'],
                [
                    'veneer_bosh_deployment_instance_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'job_name' => $_bosh['instance']['job'],
                        'job_index' => $_bosh['instance']['index'],
                    ],
                ],
                [
                    'glyphicon' => 'stop',
                    'expanded' => true,
                ]
            );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstance:summary.html.twig',
            [
                'data' => $_bosh['instance'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
    
    public function vmAction($_bosh, $_format)
    {
        if (!$_bosh['instance']['vm']) {
            throw new NotFoundHttpException();
        }
        
        return $this->redirectToRoute(
            'veneer_bosh_deployment_vm_summary',
            [
                'deployment' => $_bosh['deployment']['name'],
                'agent' => $_bosh['instance']['vm']['agentId'],
                '_format' => $_format,
            ]
        );
    }
}

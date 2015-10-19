<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentVmALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentController::defNav($nav, $_bosh)
            ->add(
                'vms',
                [
                    'veneer_bosh_deployment_vmALL_index' => [
                        'deployment' => $_bosh['deployment']['name'],
                    ],
                ]
            )
        ;
    }

    public function indexAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentVmALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Vms')
                    ->findBy(
                        [
                            'deployment' => $_bosh['deployment'],
                        ],
                        [
                            'agentId' => 'ASC',
                        ]
                    ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

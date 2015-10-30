<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentJobIndexController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentJobIndexALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['index']['index'],
                [
                    'veneer_bosh_deployment_job_index_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'job' => $_bosh['job']['job'],
                        'index' => $_bosh['index']['index'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentJobIndex:summary.html.twig',
            [
                'data' => $_bosh['index'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
    
    public function vmAction($_bosh, $_format)
    {
        if (!$_bosh['index']['vm']) {
            throw new NotFoundHttpException();
        }
        
        return $this->redirectToRoute(
            'veneer_bosh_deployment_vm_summary',
            [
                'deployment' => $_bosh['deployment']['name'],
                'agent' => $_bosh['index']['vm']['agentId'],
                '_format' => $_format,
            ]
        );
    }
}

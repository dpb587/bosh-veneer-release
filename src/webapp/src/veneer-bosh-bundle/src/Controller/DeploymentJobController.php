<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentJobController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentJobALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['job']['job'],
                [
                    'veneer_bosh_deployment_job_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'job' => $_bosh['job']['job'],
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
            'VeneerBoshBundle:DeploymentJob:summary.html.twig',
            [
                'data' => $_bosh['job'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

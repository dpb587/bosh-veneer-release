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
    public static function defNav(Breadcrumbs $nav, $_context)
    {
        return DeploymentController::defNav($nav, $_context)
            ->add(
                $_context['instance']['job'] . '/' . $_context['instance']['index'],
                [
                    'veneer_bosh_deployment_instance_summary' => [
                        'deployment' => $_context['deployment']['name'],
                        'job_name' => $_context['instance']['job'],
                        'job_index' => $_context['instance']['index'],
                    ],
                ],
                [
                    'glyphicon' => 'stop',
                    'expanded' => true,
                ]
            );
    }

    public function summaryAction($_context)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstance:summary.html.twig',
            [
                'data' => $_context['instance'],
                'endpoints' => $this->container->get('veneer_bosh.plugin_factory')->getEndpoints('bosh/deployment/instance', $_context),
                'references' => $this->container->get('veneer_bosh.plugin_factory')->getUserReferenceLinks('bosh/deployment/instance', $_context),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_context),
            ]
        );
    }
    
    public function vmAction($_context, $_format)
    {
        if (!$_context['instance']['vm']) {
            throw new NotFoundHttpException();
        }
        
        return $this->redirectToRoute(
            'veneer_bosh_deployment_vm_summary',
            [
                'deployment' => $_context['deployment']['name'],
                'agent' => $_context['instance']['vm']['agentId'],
                '_format' => $_format,
            ]
        );
    }
}

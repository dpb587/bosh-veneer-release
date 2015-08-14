<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;
use Bosh\WebBundle\Service\Breadcrumbs;

class DeploymentInstanceController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_context)
    {
        return DeploymentController::defNav($nav, $_context)
            ->add(
                $_context['instance']['job'] . '/' . $_context['instance']['index'],
                [
                    'bosh_core_deployment_instance_summary' => [
                        'deployment' => $_context['deployment']['name'],
                        'job_name' => $_context['instance']['job'],
                        'job_index' => $_context['instance']['index'],
                    ],
                ],
                [
                    'glyphicon' => 'hdd',
                    'expanded' => true,
                ]
            );
    }

    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentInstance:summary.html.twig',
            [
                'data' => $_context['instance'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/deployment/instance', $_context),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/deployment/instance', $_context),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
    
    public function vmAction($_context, $_format)
    {
        if (!$_context['instance']['vm']) {
            throw new NotFoundHttpException();
        }
        
        return $this->redirectToRoute(
            'bosh_core_deployment_vm_summary',
            [
                'deployment' => $_context['deployment']['name'],
                'agent' => $_context['instance']['vm']['agentId'],
                '_format' => $_format,
            ]
        );
    }
}

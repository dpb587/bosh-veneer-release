<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class DeploymentInstancePersistentDiskController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_context)
    {
        return DeploymentInstanceController::defNav($nav, $_context)
            ->add(
                $_context['persistent_disk']['size'] . ' MB',
                [
                    'veneer_bosh_deployment_instance_persistentdisk_summary' => [
                        'deployment' => $_context['deployment']['name'],
                        'job_name' => $_context['instance']['job'],
                        'job_index' => $_context['instance']['index'],
                        'persistent_disk' => $_context['persistent_disk']['id'],
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
            'VeneerBoshBundle:DeploymentInstancePersistentDisk:summary.html.twig',
            [
                'data' => $_context['persistent_disk'],
                'endpoints' => $this->container->get('veneer_bosh.plugin_factory')->getEndpoints('bosh/deployment/instance/persistent_disk', $_context),
                'references' => $this->container->get('veneer_bosh.plugin_factory')->getUserReferenceLinks('bosh/deployment/instance/persistent_disk', $_context),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_context),
            ]
        );
    }
    
    public function cpiAction(Request $request, $_context)
    {
        return $this->forward(
            'VeneerAwsCpiBundle:CoreDeploymentInstancePersistentDisk:cpi',
            [
                '_context' => $_context,
                '_route' => $request->attributes->get('_route'),
                '_route_params' => $request->attributes->get('_route_params'),
            ],
            $request->query->all()
        );
    }
}

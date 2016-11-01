<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentVmController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentVmALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['vm']['agentId'],
                [
                    'veneer_bosh_deployment_vm_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'agent' => $_bosh['vm']['agentId'],
                    ],
                ]
            );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentVm:summary.html.twig',
            [
                'data' => $_bosh['vm'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function instanceAction($_bosh, $_format)
    {
        $instance = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('VeneerBoshBundle:Instances')
            ->findOneBy([
                'vm' => $_bosh['vm'],
            ]);

        if (!$instance) {
            throw new NotFoundHttpException();
        }

        return $this->redirectToRoute(
            'veneer_bosh_deployment_instancegroup_instance_summary',
            [
                'deployment' => $_bosh['deployment']['name'],
                'instance_group' => $instance['instance_group'],
                'instance' => $instance['uuid'],
                '_format' => $_format,
            ]
        );
    }
    
    public function applyspecAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentVm:applyspec.html.twig',
            $_bosh['vm']['applySpecJsonAsArray'],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
    
    public function packagesAction($_bosh)
    {        
        $results = $_bosh['vm']['applySpecJsonAsArray']['packages'];
        
        usort(
            $results,
            function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentVm:packages.html.twig',
            [
                'results' => $results,
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
    
    public function templatesAction($_bosh)
    {
        $results = $_bosh['vm']['applySpecJsonAsArray']['job']['templates'];
        
        usort(
            $results,
            function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentVm:templates.html.twig',
            [
                'results' => $results,
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

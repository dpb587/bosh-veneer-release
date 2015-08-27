<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class DeploymentVmController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentController::defNav($nav, $_bosh)
            ->add(
                'VM (' . (isset($_bosh['vm']['applySpecJsonAsArray']['job']['name']) ? ($_bosh['vm']['applySpecJsonAsArray']['job']['name'] . '/' . $_bosh['vm']['applySpecJsonAsArray']['index']) : substr($_bosh['vm']['agentId'], 0, 7)) . ')',
                [
                    'veneer_bosh_deployment_vm_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'agent' => $_bosh['vm']['agentId'],
                    ],
                ],
                [
                    'glyphicon' => 'screenshot', // changeme
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
            'veneer_bosh_deployment_instance_summary',
            [
                'deployment' => $_bosh['deployment']['name'],
                'job_name' => $instance['job'],
                'job_index' => $instance['index'],
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

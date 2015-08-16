<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;
use Bosh\WebBundle\Service\Breadcrumbs;

class DeploymentVmController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_context)
    {
        return DeploymentController::defNav($nav, $_context)
            ->add(
                'VM (' . (isset($_context['vm']['applySpecJsonAsArray']['job']['name']) ? ($_context['vm']['applySpecJsonAsArray']['job']['name'] . '/' . $_context['vm']['applySpecJsonAsArray']['index']) : substr($_context['vm']['agentId'], 0, 7)) . ')',
                [
                    'bosh_core_deployment_vm_summary' => [
                        'deployment' => $_context['deployment']['name'],
                        'agent' => $_context['vm']['agentId'],
                    ],
                ],
                [
                    'glyphicon' => 'screenshot', // changeme
                ]
            );
    }

    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:summary.html.twig',
            [
                'data' => $_context['vm'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/deployment/vm', $_context),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/deployment/vm', $_context),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }

    public function instanceAction($_context, $_format)
    {
        $instance = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('BoshCoreBundle:Instances')
            ->findOneBy([
                'vm' => $_context['vm'],
            ]);

        if (!$instance) {
            throw new NotFoundHttpException();
        }

        return $this->redirectToRoute(
            'bosh_core_deployment_instance_summary',
            [
                'deployment' => $_context['deployment']['name'],
                'job_name' => $instance['job'],
                'job_index' => $instance['index'],
                '_format' => $_format,
            ]
        );
    }
    
    public function applyspecAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:applyspec.html.twig',
            $_context['vm']['applySpecJsonAsArray'],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
    
    public function packagesAction($_context)
    {        
        $results = $_context['vm']['applySpecJsonAsArray']['packages'];
        
        usort(
            $results,
            function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:packages.html.twig',
            [
                'results' => $results,
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
    
    public function templatesAction($_context)
    {
        $results = $_context['vm']['applySpecJsonAsArray']['job']['templates'];
        
        usort(
            $results,
            function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:templates.html.twig',
            [
                'results' => $results,
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
}

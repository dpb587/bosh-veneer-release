<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentVmController extends AbstractController
{
    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:summary.html.twig',
            [
                'result' => $_context['vm'],
            ],
            [
                'applyspec' => $this->generateUrl(
                    'bosh_core_deployment_vm_applyspec',
                    [
                        'deployment' => $_context['deployment']['name'],
                        'agent' => $_context['vm']['agentId'],
                    ]
                ),
                'packages' => $this->generateUrl(
                    'bosh_core_deployment_vm_packages',
                    [
                        'deployment' => $_context['deployment']['name'],
                        'agent' => $_context['vm']['agentId'],
                    ]
                ),
                'templates' => $this->generateUrl(
                    'bosh_core_deployment_vm_templates',
                    [
                        'deployment' => $_context['deployment']['name'],
                        'agent' => $_context['vm']['agentId'],
                    ]
                ),
                'networkALL' => $this->generateUrl(
                    'bosh_core_deployment_vm_networkALL_index',
                    [
                        'deployment' => $_context['deployment']['name'],
                        'agent' => $_context['vm']['agentId'],
                    ]
                ),
            ]
        );
    }
    
    public function applyspecAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:applyspec.html.twig',
            $_context['vm']['applySpecJsonAsArray']
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
            ]
        );
    }
}

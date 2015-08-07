<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeploymentVmController extends AbstractDeploymentVmController
{
    public function indexAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:index.html.twig',
            $context,
            [
                'result' => $context['vm'],
            ],
            [
                'applyspec' => $this->generateUrl(
                    'bosh_core_deployment_vm_applyspec',
                    [
                        'deployment' => $context['deployment']['name'],
                        'agent' => $context['vm']['agentId'],
                    ]
                ),
                'packages' => $this->generateUrl(
                    'bosh_core_deployment_vm_packages',
                    [
                        'deployment' => $context['deployment']['name'],
                        'agent' => $context['vm']['agentId'],
                    ]
                ),
                'templates' => $this->generateUrl(
                    'bosh_core_deployment_vm_templates',
                    [
                        'deployment' => $context['deployment']['name'],
                        'agent' => $context['vm']['agentId'],
                    ]
                ),
                'networkALL' => $this->generateUrl(
                    'bosh_core_deployment_vm_networkALL_index',
                    [
                        'deployment' => $context['deployment']['name'],
                        'agent' => $context['vm']['agentId'],
                    ]
                ),
            ]
        );
    }
    
    public function applyspecAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:applyspec.html.twig',
            $context,
            $context['vm']['applySpecJsonAsArray']
        );
    }
    
    public function packagesAction(Request $request)
    {
        $context = $this->validateRequest($request);
        
        $results = $context['vm']['applySpecJsonAsArray']['packages'];
        
        usort(
            $results,
            function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:packages.html.twig',
            $context,
            [
                'results' => $results,
            ]
        );
    }
    
    public function templatesAction(Request $request)
    {
        $context = $this->validateRequest($request);

        $results = $context['vm']['applySpecJsonAsArray']['job']['templates'];
        
        usort(
            $results,
            function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            }
        );

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVm:templates.html.twig',
            $context,
            [
                'results' => $results,
            ]
        );
    }
}

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
                'data' => $_context['vm'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/deployment/vm', $_context),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/deployment/vm', $_context),
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

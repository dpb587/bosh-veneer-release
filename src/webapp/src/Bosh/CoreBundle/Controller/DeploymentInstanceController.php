<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentInstanceController extends AbstractController
{
    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentInstance:summary.html.twig',
            [
                'data' => $_context['instance'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/deployment/instance', $_context),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/deployment/instance', $_context),
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

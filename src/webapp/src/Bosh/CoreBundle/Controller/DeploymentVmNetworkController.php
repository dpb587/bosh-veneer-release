<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeploymentVmNetworkController extends AbstractDeploymentVmNetworkController
{
    public function indexAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVmNetwork:index.html.twig',
            $context,
            [
                'result' => $context['network'],
            ],
            [
                'cpi' => $this->generateUrl(
                    'bosh_core_deployment_vm_network_cpi',
                    [
                        'deployment' => $context['deployment']['name'],
                        'agent' => $context['vm']['agentId'],
                        'network' => $context['network']['name'],
                    ]
                ),
            ]
        );
    }
    
    public function cpiAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->forward(
            'BoshAwsCpiBundle:CoreDeploymentVmNetwork:cpi',
            [
                'context' => $context,
                '_route' => $request->attributes->get('_route'),
                '_route_params' => $request->attributes->get('_route_params'),
            ],
            $request->query->all()
        );
    }
}

<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentVmNetworkController extends AbstractController
{
    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentVmNetwork:summary.html.twig',
            [
                'result' => $_context['network'],
            ],
            [
                'cpi' => $this->generateUrl(
                    'bosh_core_deployment_vm_network_cpi',
                    [
                        'deployment' => $_context['deployment']['name'],
                        'agent' => $_context['vm']['agentId'],
                        'network' => $_context['network']['name'],
                    ]
                ),
            ]
        );
    }
    
    public function cpiAction(Request $request, $_context)
    {
        return $this->forward(
            'BoshAwsCpiBundle:CoreDeploymentVmNetwork:cpi',
            [
                '_context' => $_context,
                '_route' => $request->attributes->get('_route'),
                '_route_params' => $request->attributes->get('_route_params'),
            ],
            $request->query->all()
        );
    }
}

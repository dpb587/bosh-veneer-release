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
                'data' => $_context['network'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/deployment/vm/network', $_context),
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

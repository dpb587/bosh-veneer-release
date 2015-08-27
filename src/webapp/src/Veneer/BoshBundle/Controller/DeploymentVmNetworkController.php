<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;

class DeploymentVmNetworkController extends AbstractController
{
    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentVmNetwork:summary.html.twig',
            [
                'data' => $_bosh['network'],
            ]
        );
    }
    
    public function cpiAction(Request $request, $_bosh)
    {
        return $this->forward(
            'VeneerAwsCpiBundle:CoreDeploymentVmNetwork:cpi',
            [
                '_context' => $_bosh,
                '_route' => $request->attributes->get('_route'),
                '_route_params' => $request->attributes->get('_route_params'),
            ],
            $request->query->all()
        );
    }
}

<?php

namespace Veneer\AwsCpiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;

class CoreDeploymentVmNetworkController extends AbstractController
{
    public function cpiAction(array $_context)
    {
        return $this->renderApi(
            'VeneerAwsCpiBundle:CoreDeploymentVmNetworkController:cpi.html.twig',
            [
                'properties' => $_context['network']['cloud_properties'],
            ]
        );
    }
}

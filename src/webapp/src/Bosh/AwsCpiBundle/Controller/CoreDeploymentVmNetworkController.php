<?php

namespace Bosh\AwsCpiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\CoreBundle\Controller\AbstractController;

class CoreDeploymentVmNetworkController extends AbstractController
{
    public function cpiAction(Request $request, array $context)
    {
        return $this->renderApi(
            'BoshAwsCpiBundle:CoreDeploymentVmNetworkController:cpi.html.twig',
            $context,
            [
                'result' => $context['network']['cloud_properties'],
            ]
        );
    }
}

<?php

namespace Bosh\AwsCpiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class CoreDeploymentVmResourcePoolController extends AbstractController
{
    public function cpiAction(array $_context)
    {
        return $this->renderApi(
            'BoshAwsCpiBundle:CoreDeploymentVmResourcePoolController:cpi.html.twig',
            [
                'properties' => $_context['vm']['applySpecJsonAsArray']['resource_pool']['cloud_properties'],
            ]
        );
    }
}

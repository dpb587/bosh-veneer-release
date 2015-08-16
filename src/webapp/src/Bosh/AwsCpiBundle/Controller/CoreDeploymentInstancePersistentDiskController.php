<?php

namespace Bosh\AwsCpiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class CoreDeploymentInstancePersistentDiskController extends AbstractController
{
    public function cpiAction(array $_context)
    {
        return $this->renderApi(
            'BoshAwsCpiBundle:CoreDeploymentInstancePersistentDiskController:cpi.html.twig',
            [
                'properties' => $_context['persistent_disk']['cloudPropertiesJsonAsArray'],
            ]
        );
    }
}

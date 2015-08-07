<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractDeploymentController extends AbstractController
{
    protected function validateRequest(Request $request)
    {
        $context = parent::validateRequest($request);

        $deployment = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('BoshCoreBundle:Deployments')
            ->findOneByName($request->attributes->get('deployment'));
        
        if (!$deployment) {
            throw new NotFoundHttpException();
        }

        $context['deployment'] = $deployment;
        
        return $context;
    }
}

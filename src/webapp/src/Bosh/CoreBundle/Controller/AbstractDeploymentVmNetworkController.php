<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractDeploymentVmNetworkController extends AbstractDeploymentVmController
{
    protected function validateRequest(Request $request)
    {
        $context = parent::validateRequest($request);

        if (!isset($context['vm']['applySpecJsonAsArray']['networks'][$request->attributes->get('network')])) {
            throw new NotFoundHttpException();
        }

        $context['network'] = $context['vm']['applySpecJsonAsArray']['networks'][$request->attributes->get('network')];
        $context['network']['name'] = $request->attributes->get('network');
        
        return $context;
    }
}

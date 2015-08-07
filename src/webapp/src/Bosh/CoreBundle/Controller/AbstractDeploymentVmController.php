<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractDeploymentVmController extends AbstractDeploymentController
{
    protected function validateRequest(Request $request)
    {
        $context = parent::validateRequest($request);

        $vm = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('BoshCoreBundle:Vms')
            ->findOneBy([
                'deployment' => $context['deployment'],
                'agentId' => $request->attributes->get('agent'),
            ]);
        
        if (!$vm) {
            throw new NotFoundHttpException();
        }

        $context['vm'] = $vm;
        
        return $context;
    }
}

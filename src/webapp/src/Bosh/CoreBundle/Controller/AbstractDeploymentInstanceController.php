<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractDeploymentInstanceController extends AbstractDeploymentController
{
    protected function validateRequest(Request $request)
    {
        $context = parent::validateRequest($request);

        $instance = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('BoshCoreBundle:Instances')
            ->findOneBy([
                'deployment' => $context['deployment'],
                'job' => $request->attributes->get('job_name'),
                'index' => $request->attributes->get('job_index'),
            ]);
        
        if (!$instance) {
            throw new NotFoundHttpException();
        }

        $context['instance'] = $instance;
        
        return $context;
    }
}

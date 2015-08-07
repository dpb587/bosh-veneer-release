<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeploymentInstanceController extends AbstractDeploymentInstanceController
{
    public function indexAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:DeploymentInstance:index.html.twig',
            $context,
            [
                'result' => $context['instance'],
            ],
            [
                'vm' => $this->generateUrl(
                    'bosh_core_deployment_instance_vm',
                    [
                        'deployment' => $context['deployment']['name'],
                        'job_name' => $context['instance']['job'],
                        'job_index' => $context['instance']['index'],
                    ]
                ),
            ]
        );
    }
    
    public function vmAction(Request $request)
    {
        $context = $this->validateRequest($request);
        
        if (!$context['instance']['vm']) {
            throw new NotFoundHttpException();
        }
        
        return $this->redirectToRoute(
            'bosh_core_deployment_vm_index',
            [
                'deployment' => $context['deployment']['name'],
                'agent' => $context['instance']['vm']['agentId'],
                '_format' => $request->attributes->get('_format'),
            ]
        );
    }
}

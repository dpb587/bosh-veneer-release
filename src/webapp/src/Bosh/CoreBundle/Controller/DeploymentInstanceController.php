<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentInstanceController extends AbstractController
{
    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentInstance:summary.html.twig',
            [
                'result' => $_context['instance'],
            ],
            [
                'vm' => $this->generateUrl(
                    'bosh_core_deployment_instance_vm',
                    [
                        'deployment' => $_context['deployment']['name'],
                        'job_name' => $_context['instance']['job'],
                        'job_index' => $_context['instance']['index'],
                    ]
                ),
            ]
        );
    }
    
    public function vmAction($_context, $_format)
    {
        if (!$_context['instance']['vm']) {
            throw new NotFoundHttpException();
        }
        
        return $this->redirectToRoute(
            'bosh_core_deployment_vm_summary',
            [
                'deployment' => $_context['deployment']['name'],
                'agent' => $_context['instance']['vm']['agentId'],
                '_format' => $_format,
            ]
        );
    }
}

<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeploymentVmALLController extends AbstractDeploymentController
{
    public function indexAction(Request $request, $_format)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVmALL:index.html.twig',
            $context,
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('BoshCoreBundle:Vms')
                    ->findBy(
                        [
                            'deployment' => $context['deployment'],
                        ],
                        [
                            'agentId' => 'ASC',
                        ]
                    ),
            ]
        );
    }
}

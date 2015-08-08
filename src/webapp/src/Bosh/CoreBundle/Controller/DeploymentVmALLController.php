<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentVmALLController extends AbstractController
{
    public function indexAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentVmALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('BoshCoreBundle:Vms')
                    ->findBy(
                        [
                            'deployment' => $_context['deployment'],
                        ],
                        [
                            'agentId' => 'ASC',
                        ]
                    ),
            ]
        );
    }
}

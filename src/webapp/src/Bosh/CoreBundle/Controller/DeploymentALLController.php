<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentALLController extends AbstractController
{
    public function indexAction()
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentALL:index.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v
                            ->setSerializationHint('manifest', false)
                            ->setSerializationHint('cloudConfig', false)
                            ;
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:Deployments')
                        ->findBy([], [ 'name' => 'ASC' ])
                ),
            ]
        );
    }
}

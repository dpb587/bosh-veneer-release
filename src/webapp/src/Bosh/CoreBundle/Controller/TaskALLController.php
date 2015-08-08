<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class TaskALLController extends AbstractController
{
    public function indexAction()
    {
        return $this->renderApi(
            'BoshCoreBundle:TaskALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('BoshCoreBundle:Tasks')
                    ->findBy([], [ 'id' => 'DESC' ]),
            ]
        );
    }
}

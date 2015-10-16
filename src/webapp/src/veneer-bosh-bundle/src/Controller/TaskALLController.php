<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class TaskALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{task}',
            [
                'veneer_bosh_taskALL_index' => [],
            ],
            [
                'glyphicon' => 'tasks',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'VeneerBoshBundle:TaskALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Tasks')
                    ->findBy([], [ 'id' => 'DESC' ]),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs')),
            ]
        );
    }
}

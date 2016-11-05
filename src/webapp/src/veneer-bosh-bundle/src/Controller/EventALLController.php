<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class EventALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            'event',
            [
                'veneer_bosh_eventALL_index' => [],
            ],
            [
                'fontawesome' => 'tasks',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'VeneerBoshBundle:EventALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Events')
                    ->findBy([], [ 'id' => 'DESC' ]),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs')),
            ]
        );
    }
}

<?php

namespace Veneer\HubBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class HubALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            'hubs',
            [
                'veneer_hub_hubALL_index' => [],
            ],
            [
                'fontawesome' => 'map-signs',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'VeneerHubBundle:HubALL:index.html.twig',
            [
                'results' => $this->container->get('veneer_hub.hubs')->mapKeys(function ($v, $n) {
                    return [
                        'name' => $n,
                        'title' => $v->getTitle(),
                    ];
                }),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_hub.breadcrumbs')),
            ]
        );
    }
}

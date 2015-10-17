<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            'marketplaces',
            [
                'veneer_marketplace_marketplaceALL_index' => [],
            ],
            [
                'fontawesome' => 'map-signs',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'VeneerMarketplaceBundle:MarketplaceALL:index.html.twig',
            [
                'results' => $this->container->get('veneer_marketplace.marketplaces')->mapKeys(function ($v, $n) {
                    return [
                        'name' => $n,
                        'title' => $v->getTitle(),
                    ];
                }),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_marketplace.breadcrumbs')),
            ]
        );
    }
}

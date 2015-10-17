<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class MarketplaceALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{marketplace}',
            [
                'veneer_marketplace_marketplaceALL_index' => [],
            ],
            [
                'glyphicon' => 'gift',
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
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs')),
            ]
        );
    }
}

<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class MarketplaceController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return $nav->add(
            $_bosh['release']['name'],
            [
                'veneer_marketplace_marketplace_index' => [
                    'marketplace' => $_bosh['marketplace']['name'],
                ],
            ],
            [
                'glyphicon' => 'gift',
                'expanded' => true,
            ]
        );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerMarketplaceBundle:Marketplace:summary.html.twig',
            [
                'data' => $_bosh['marketplace'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

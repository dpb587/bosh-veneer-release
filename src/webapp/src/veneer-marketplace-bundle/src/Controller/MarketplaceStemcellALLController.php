<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceStemcellALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, array $_bosh)
    {
        return MarketplaceController::defNav($nav, $_bosh)->add(
            'stemcell',
            [
                'veneer_marketplace_marketplace_stemcellALL_index' => [
                    'marketplace' => $_bosh['marketplace']['name'],
                ],
            ]
        );
    }

    public function indexAction(array $_bosh)
    {
        return $this->renderApi(
            'VeneerMarketplaceBundle:MarketplaceStemcellALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.state_entity_manager')->createQuery(
                        '
                            SELECT
                                sv.stemcell AS name,
                                COUNT(sv.version) AS version_count
                            FROM VeneerMarketplaceBundle:StemcellVersion sv
                            WHERE
                                sv.marketplace = :marketplace
                            GROUP BY sv.stemcell
                            ORDER BY sv.stemcell
                        '
                    )
                    ->setParameter('marketplace', $_bosh['marketplace']['name'])
                    ->getArrayResult()
                ,
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_marketplace.breadcrumbs'), $_bosh),
            ]
        );
    }
}

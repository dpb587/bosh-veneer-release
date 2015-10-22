<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceReleaseALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, array $_bosh)
    {
        return MarketplaceController::defNav($nav, $_bosh)->add(
            'release',
            [
                'veneer_marketplace_marketplace_releaseALL_index' => [
                    'marketplace' => $_bosh['marketplace']['name'],
                ],
            ]
        );
    }

    public function indexAction(array $_bosh)
    {
        return $this->renderApi(
            'VeneerMarketplaceBundle:MarketplaceReleaseALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.state_entity_manager')->createQuery(
                        '
                            SELECT
                                rv.release AS name,
                                COUNT(rv.version) AS version_count
                            FROM VeneerMarketplaceBundle:ReleaseVersion rv
                            WHERE
                                rv.marketplace = :marketplace
                            GROUP BY rv.release
                            ORDER BY rv.release
                        '
                    )
                    ->setParameter('marketplace', $_bosh['marketplace']['name'])
                    ->getArrayResult()
                ,
                'uploaded_locally' => array_map(
                    'current',
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->createQuery(
                            '
                                SELECT
                                    r.name
                                FROM VeneerBoshBundle:Releases r
                            '
                        )
                        ->getArrayResult()
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_marketplace.breadcrumbs'), $_bosh),
            ]
        );
    }
}

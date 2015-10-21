<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return MarketplaceALLController::defNav($nav)->add(
            $_bosh['marketplace']['title'],
            [
                'veneer_marketplace_marketplace_summary' => [
                    'marketplace' => $_bosh['marketplace']['name'],
                ],
            ]
        );
    }

    public function summaryAction($_bosh)
    {
        $countReleases = $this->container->get('doctrine.orm.state_entity_manager')
            ->createQuery(
                '
                    SELECT
                        COUNT(DISTINCT rv.release)
                    FROM VeneerMarketplaceBundle:ReleaseVersion rv
                    WHERE
                        rv.marketplace = :marketplace
                '
            )
            ->setParameter('marketplace', $_bosh['marketplace']['name'])
            ->getSingleScalarResult();

        $countStemcells = $this->container->get('doctrine.orm.state_entity_manager')
            ->createQuery(
                '
                    SELECT
                        COUNT(DISTINCT sv.stemcell)
                    FROM VeneerMarketplaceBundle:StemcellVersion sv
                    WHERE
                        sv.marketplace = :marketplace
                '
            )
            ->setParameter('marketplace', $_bosh['marketplace']['name'])
            ->getSingleScalarResult();

        return $this->renderApi(
            'VeneerMarketplaceBundle:Marketplace:summary.html.twig',
            [
                'data' => $_bosh['marketplace'],
                'count' => [
                    'releases' => $countReleases,
                    'stemcells' => $countStemcells,
                ],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_marketplace.breadcrumbs'), $_bosh),
            ]
        );
    }
}

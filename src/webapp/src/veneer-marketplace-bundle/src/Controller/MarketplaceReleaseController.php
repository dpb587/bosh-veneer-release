<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceReleaseController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return MarketplaceReleaseALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['release']['name'],
                [
                    'veneer_marketplace_marketplace_release_summary' => [
                        'marketplace' => $_bosh['marketplace']['name'],
                        'release' => $_bosh['release']['name'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction($_bosh)
    {
        $stats = $this->container->get('doctrine.orm.state_entity_manager')
            ->createQuery(
                '
                    SELECT
                        MAX(rv.statLastSeenAt) AS stat_last_seen_at_max,
                        MIN(rv.tarballSize) AS tarball_size_min,
                        MAX(rv.tarballSize) AS tarball_size_max
                    FROM VeneerMarketplaceBundle:ReleaseVersion rv
                    WHERE
                        rv.marketplace = :marketplace
                        AND rv.release = :release
                '
            )
            ->setParameter('marketplace', $_bosh['marketplace']['name'])
            ->setParameter('release', $_bosh['release']['name'])
            ->getSingleResult()
        ;

        return $this->renderApi(
            'VeneerMarketplaceBundle:MarketplaceRelease:summary.html.twig',
            [
                'data' => $_bosh['release'],
                'stats' => $stats,
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_marketplace.breadcrumbs'), $_bosh),
            ]
        );
    }
}

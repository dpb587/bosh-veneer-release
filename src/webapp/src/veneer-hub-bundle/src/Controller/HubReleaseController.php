<?php

namespace Veneer\HubBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class HubReleaseController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return HubReleaseALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['release']['name'],
                [
                    'veneer_hub_hub_release_summary' => [
                        'hub' => $_bosh['hub']['name'],
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
                    FROM VeneerHubBundle:ReleaseVersion rv
                    WHERE
                        rv.hub = :hub
                        AND rv.release = :release
                '
            )
            ->setParameter('hub', $_bosh['hub']['name'])
            ->setParameter('release', $_bosh['release']['name'])
            ->getSingleResult()
        ;

        return $this->renderApi(
            'VeneerHubBundle:HubRelease:summary.html.twig',
            [
                'data' => $_bosh['release'],
                'stats' => $stats,
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_hub.breadcrumbs'), $_bosh),
            ]
        );
    }
}

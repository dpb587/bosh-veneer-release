<?php

namespace Veneer\HubBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class HubController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return HubALLController::defNav($nav)->add(
            $_bosh['hub']['title'],
            [
                'veneer_hub_hub_summary' => [
                    'hub' => $_bosh['hub']['name'],
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
                    FROM VeneerHubBundle:ReleaseVersion rv
                    WHERE
                        rv.hub = :hub
                '
            )
            ->setParameter('hub', $_bosh['hub']['name'])
            ->getSingleScalarResult();

        $countStemcells = $this->container->get('doctrine.orm.state_entity_manager')
            ->createQuery(
                '
                    SELECT
                        COUNT(DISTINCT sv.stemcell)
                    FROM VeneerHubBundle:StemcellVersion sv
                    WHERE
                        sv.hub = :hub
                '
            )
            ->setParameter('hub', $_bosh['hub']['name'])
            ->getSingleScalarResult();

        return $this->renderApi(
            'VeneerHubBundle:Hub:summary.html.twig',
            [
                'data' => $_bosh['hub'],
                'count' => [
                    'releases' => $countReleases,
                    'stemcells' => $countStemcells,
                ],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_hub.breadcrumbs'), $_bosh),
            ]
        );
    }
}

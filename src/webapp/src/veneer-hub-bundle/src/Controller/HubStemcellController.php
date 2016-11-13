<?php

namespace Veneer\HubBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\HubBundle\Plugin\RequestContext\Annotations as HubContext;

/**
 * @HubContext\HubStemcell
 */
class HubStemcellController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return HubStemcellALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['stemcell']['name'],
                [
                    'veneer_hub_hub_stemcell_summary' => [
                        'hub' => $_bosh['hub']['name'],
                        'stemcell' => $_bosh['stemcell']['name'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
    {
        $stats = $this->container->get('doctrine.orm.state_entity_manager')
            ->createQuery(
                '
                    SELECT
                        MAX(rv.statLastSeenAt) AS stat_last_seen_at_max,
                        MIN(rv.tarballSize) AS tarball_size_min,
                        MAX(rv.tarballSize) AS tarball_size_max
                    FROM VeneerHubBundle:StemcellVersion rv
                    WHERE
                        rv.hub = :hub
                        AND rv.stemcell = :stemcell
                '
            )
            ->setParameter('hub', $_bosh['hub']['name'])
            ->setParameter('stemcell', $_bosh['stemcell']['name'])
            ->getSingleResult()
        ;

        return $this->renderApi(
            'VeneerHubBundle:HubStemcell:summary.html.twig',
            [
                'data' => $_bosh['stemcell'],
                'stats' => $stats,
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_hub.breadcrumbs'), $_bosh),
            ]
        );
    }
}

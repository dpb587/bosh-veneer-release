<?php

namespace Veneer\HubBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class HubReleaseALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, array $_bosh)
    {
        return HubController::defNav($nav, $_bosh)->add(
            'release',
            [
                'veneer_hub_hub_releaseALL_index' => [
                    'hub' => $_bosh['hub']['name'],
                ],
            ]
        );
    }

    public function indexAction(array $_bosh)
    {
        return $this->renderApi(
            'VeneerHubBundle:HubReleaseALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.state_entity_manager')->createQuery(
                        '
                            SELECT
                                rv.release AS name,
                                COUNT(rv.version) AS version_count
                            FROM VeneerHubBundle:ReleaseVersion rv
                            WHERE
                                rv.hub = :hub
                            GROUP BY rv.release
                            ORDER BY rv.release
                        '
                    )
                    ->setParameter('hub', $_bosh['hub']['name'])
                    ->getArrayResult(),
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
                'def_nav' => static::defNav($this->container->get('veneer_hub.breadcrumbs'), $_bosh),
            ]
        );
    }
}

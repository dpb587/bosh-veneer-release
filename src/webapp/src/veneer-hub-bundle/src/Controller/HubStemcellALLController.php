<?php

namespace Veneer\HubBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\HubBundle\Plugin\RequestContext\Annotations as HubContext;

/**
 * @HubContext\Hub
 */
class HubStemcellALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return HubController::defNav($nav, $_bosh)->add(
            'stemcell',
            [
                'veneer_hub_hub_stemcellALL_index' => [
                    'hub' => $_bosh['hub']['name'],
                ],
            ],
            [
                'fontawesome' => 'archive',
            ]
        );
    }

    public function indexAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerHubBundle:HubStemcellALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.state_entity_manager')->createQuery(
                        '
                            SELECT
                                sv.stemcell AS name,
                                COUNT(sv.version) AS version_count
                            FROM VeneerHubBundle:StemcellVersion sv
                            WHERE
                                sv.hub = :hub
                            GROUP BY sv.stemcell
                            ORDER BY sv.stemcell
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
                                    s.name
                                FROM VeneerBoshBundle:Stemcells s
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

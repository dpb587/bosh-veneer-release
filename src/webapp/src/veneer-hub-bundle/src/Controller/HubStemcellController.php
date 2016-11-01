<?php

namespace Veneer\HubBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class HubStemcellController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
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

    public function summaryAction($_bosh)
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

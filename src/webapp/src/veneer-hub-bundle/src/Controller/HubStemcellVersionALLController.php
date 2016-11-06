<?php

namespace Veneer\HubBundle\Controller;

use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class HubStemcellVersionALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return HubStemcellController::defNav($nav, $_bosh)
            ->add(
                'versions',
                [
                    'veneer_hub_hub_stemcell_versionALL_index' => [
                        'hub' => $_bosh['hub']['name'],
                        'stemcell' => $_bosh['stemcell']['name'],
                    ],
                ]
            )
        ;
    }
    public function indexAction(array $_bosh)
    {
        $results = $this->container->get('doctrine.orm.state_entity_manager')
            ->getRepository('VeneerHubBundle:StemcellVersion')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.hub', '=', ':hub'))->setParameter('hub', $_bosh['hub']['name'])
            ->andWhere(new Expr\Comparison('v.stemcell', '=', ':stemcell'))->setParameter('stemcell', $_bosh['stemcell']['name'])
            ->addOrderBy('v.semverMajor', 'DESC')
            ->addOrderBy('v.semverMinor', 'DESC')
            ->addOrderBy('v.semverPatch', 'DESC')
            ->addOrderBy('v.semverExtra', 'DESC')
            ->addOrderBy('v.version', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->renderApi(
            'VeneerHubBundle:HubStemcellVersionALL:index.html.twig',
            [
                'results' => $results,
                'uploaded_locally' => array_map(
                    'current',
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->createQuery(
                            '
                                SELECT
                                    s.version
                                FROM VeneerBoshBundle:Stemcells s
                                WHERE
                                    s.name = :stemcell
                            '
                        )
                        ->setParameter('stemcell', $_bosh['stemcell']['name'])
                        ->getArrayResult()
                ),
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_hub.breadcrumbs'), $_bosh),
            ]
        );
    }
}

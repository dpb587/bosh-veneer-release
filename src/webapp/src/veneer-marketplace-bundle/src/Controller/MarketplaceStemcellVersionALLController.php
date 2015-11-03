<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceStemcellVersionALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return MarketplaceStemcellController::defNav($nav, $_bosh)
            ->add(
                'versions',
                [
                    'veneer_marketplace_marketplace_stemcell_versionALL_index' => [
                        'marketplace' => $_bosh['marketplace']['name'],
                        'stemcell' => $_bosh['stemcell']['name'],
                    ],
                ]
            )
        ;
    }
    public function indexAction(array $_bosh)
    {
        $results = $this->container->get('doctrine.orm.state_entity_manager')
            ->getRepository('VeneerMarketplaceBundle:StemcellVersion')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.marketplace', '=', ':marketplace'))->setParameter('marketplace', $_bosh['marketplace']['name'])
            ->andWhere(new Expr\Comparison('v.stemcell', '=', ':stemcell'))->setParameter('stemcell', $_bosh['stemcell']['name'])
            ->addOrderBy('v.version')
            ->getQuery()
            ->getResult();
        
        usort(
            $results,
            function($a, $b) {
                return -1 * version_compare($a->getVersion(), $b->getVersion());
            }
        );

        return $this->renderApi(
            'VeneerMarketplaceBundle:MarketplaceStemcellVersionALL:index.html.twig',
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
                'def_nav' => self::defNav($this->container->get('veneer_marketplace.breadcrumbs'), $_bosh),
            ]
        );
    }
}

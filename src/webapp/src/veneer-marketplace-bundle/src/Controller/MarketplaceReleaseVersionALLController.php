<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceReleaseVersionALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return MarketplaceReleaseController::defNav($nav, $_bosh)
            ->add(
                'versions',
                [
                    'veneer_marketplace_marketplace_release_versionALL_index' => [
                        'marketplace' => $_bosh['marketplace']['name'],
                        'release' => $_bosh['release']['name'],
                    ],
                ]
            )
        ;
    }
    public function indexAction(array $_bosh)
    {
        $results = $this->container->get('doctrine.orm.state_entity_manager')
            ->getRepository('VeneerMarketplaceBundle:ReleaseVersion')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.marketplace', '=', ':marketplace'))->setParameter('marketplace', $_bosh['marketplace']['name'])
            ->andWhere(new Expr\Comparison('v.release', '=', ':release'))->setParameter('release', $_bosh['release']['name'])
            ->addOrderBy('v.semverMajor', 'DESC')
            ->addOrderBy('v.semverMinor', 'DESC')
            ->addOrderBy('v.semverPatch', 'DESC')
            ->addOrderBy('v.semverExtra', 'DESC')
            ->addOrderBy('v.version', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->renderApi(
            'VeneerMarketplaceBundle:MarketplaceReleaseVersionALL:index.html.twig',
            [
                'results' => $results,
                'uploaded_locally' => array_map(
                    'current',
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->createQuery(
                            '
                                SELECT
                                    rv.version
                                FROM VeneerBoshBundle:ReleaseVersions rv
                                JOIN rv.release r
                                WHERE
                                    r.name = :release
                            '
                        )
                        ->setParameter('release', $_bosh['release']['name'])
                        ->getArrayResult()
                ),
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_marketplace.breadcrumbs'), $_bosh),
            ]
        );
    }
}

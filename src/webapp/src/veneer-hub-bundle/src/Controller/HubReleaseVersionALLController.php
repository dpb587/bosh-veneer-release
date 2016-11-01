<?php

namespace Veneer\HubBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class HubReleaseVersionALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return HubReleaseController::defNav($nav, $_bosh)
            ->add(
                'versions',
                [
                    'veneer_hub_hub_release_versionALL_index' => [
                        'hub' => $_bosh['hub']['name'],
                        'release' => $_bosh['release']['name'],
                    ],
                ]
            )
        ;
    }
    public function indexAction(array $_bosh)
    {
        $results = $this->container->get('doctrine.orm.state_entity_manager')
            ->getRepository('VeneerHubBundle:ReleaseVersion')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.hub', '=', ':hub'))->setParameter('hub', $_bosh['hub']['name'])
            ->andWhere(new Expr\Comparison('v.release', '=', ':release'))->setParameter('release', $_bosh['release']['name'])
            ->addOrderBy('v.semverMajor', 'DESC')
            ->addOrderBy('v.semverMinor', 'DESC')
            ->addOrderBy('v.semverPatch', 'DESC')
            ->addOrderBy('v.semverExtra', 'DESC')
            ->addOrderBy('v.version', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->renderApi(
            'VeneerHubBundle:HubReleaseVersionALL:index.html.twig',
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
                'def_nav' => self::defNav($this->container->get('veneer_hub.breadcrumbs'), $_bosh),
            ]
        );
    }
}

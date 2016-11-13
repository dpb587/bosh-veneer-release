<?php

namespace Veneer\BoshBundle\Controller;

use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\Release
 */
class ReleaseVersionALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return ReleaseController::defNav($nav, $_bosh)
            ->add(
                'version',
                [
                    'veneer_bosh_release_versionALL_index' => [
                        'release' => $_bosh['release']['name'],
                    ],
                ],
                [
                    'fontawesome' => 'code-fork',
                ]
            )
        ;
    }

    public function indexAction(Context $_bosh)
    {
        $results = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('VeneerBoshBundle:ReleaseVersions')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.release', '=', ':release'))->setParameter('release', $_bosh['release'])
            ->addOrderBy('v.version')
            ->getQuery()
            ->getResult();

        usort(
            $results,
            function ($a, $b) {
                return -1 * version_compare($a['version'], $b['version']);
            }
        );

        return $this->renderApi(
            'VeneerBoshBundle:ReleaseVersionALL:index.html.twig',
            [
                'results' => $results,
            ]
        );
    }
}

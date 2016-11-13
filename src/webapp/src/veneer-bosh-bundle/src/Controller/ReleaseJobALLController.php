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
class ReleaseJobALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return ReleaseController::defNav($nav, $_bosh)
            ->add(
                'job',
                [
                    'veneer_bosh_release_jobALL_index' => [
                        'release' => $_bosh['release']['name'],
                    ],
                ]
            );
    }

    public function indexAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseJobALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Templates')
                    ->createQueryBuilder('t')
                    ->andWhere(new Expr\Comparison('t.release', '=', ':release'))->setParameter('release', $_bosh['release'])
                    ->addOrderBy('t.name')
                    ->addOrderBy('t.version')
                    ->getQuery()
                    ->getResult(),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

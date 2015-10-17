<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class ReleaseTemplateALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return ReleaseController::defNav($nav, $_bosh)
            ->add(
                'templates',
                [
                    'veneer_bosh_release_templateALL_index' => [
                        'release' => $_bosh['release']['name'],
                    ],
                ]
            );
    }

    public function indexAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseTemplateALL:index.html.twig',
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

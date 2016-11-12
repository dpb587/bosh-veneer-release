<?php

namespace Veneer\BoshBundle\Controller;

use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class StemcellVersionALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return StemcellController::defNav($nav, $_bosh)
            ->add(
                'version',
                [
                    'veneer_bosh_stemcell_versionALL_index' => [
                        'stemcell' => $_bosh['stemcell']['name'],
                    ],
                ],
                [
                    'fontawesome' => 'code-fork',
                ]
            )
        ;
    }
    public function indexAction($_bosh)
    {
        $results = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('VeneerBoshBundle:Stemcells')
            ->createQueryBuilder('s')
            ->andWhere(new Expr\Comparison('s.name', '=', ':stemcell'))->setParameter('stemcell', $_bosh['stemcell']['name'])
            ->addOrderBy('s.version')
            ->getQuery()
            ->getResult();

        usort(
            $results,
            function ($a, $b) {
                return -1 * version_compare($a['version'], $b['version']);
            }
        );

        return $this->renderApi(
            'VeneerBoshBundle:StemcellVersionALL:index.html.twig',
            [
                'results' => $results,
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

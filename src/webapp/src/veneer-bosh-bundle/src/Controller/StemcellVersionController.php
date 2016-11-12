<?php

namespace Veneer\BoshBundle\Controller;

use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;

class StemcellVersionController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return StemcellVersionALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['version']['version'],
                [
                    'veneer_bosh_stemcell_version_summary' => [
                        'stemcell' => $_bosh['stemcell']['name'],
                        'version' => $_bosh['version']['version'],
                    ],
                ],
                [
                    'fontawesome' => 'record',
                    'expanded' => true,
                ]
            );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:StemcellVersion:summary.html.twig',
            [
                'data' => $_bosh['version'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function deploymentsAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:StemcellVersion:deployments.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['deployment'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('VeneerBoshBundle:DeploymentsStemcells')
                        ->createQueryBuilder('ds')
                        ->join('ds.deployment', 'd')->addSelect('d')
                        ->where(new Expr\Comparison('ds.stemcell', '=', ':stemcell'))->setParameter('stemcell', $_bosh['version'])
                        ->orderBy('d.name')
                        ->getQuery()
                        ->getResult()
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

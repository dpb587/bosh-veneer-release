<?php

namespace Veneer\BoshBundle\Controller;

use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\Deployment
 */
class DeploymentController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return DeploymentALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['deployment']['name'],
                [
                    'veneer_bosh_deployment_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Deployment:summary.html.twig',
            [
                'data' => $_bosh['deployment'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function manifestAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Deployment:manifest.html.twig',
            [
                'string' => $_bosh['deployment']['manifest'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function releasesAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Deployment:releases.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['releaseVersion']
                            ->setSerializationHint('release', true)
                            ;
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('VeneerBoshBundle:DeploymentsReleaseVersions')
                        ->createQueryBuilder('drv')
                        ->join('drv.releaseVersion', 'rv')->addSelect('rv')
                        ->join('rv.release', 'r')->addSelect('r')
                        ->where(new Expr\Comparison('drv.deployment', '=', ':deployment'))->setParameter('deployment', $_bosh['deployment'])
                        ->orderBy('r.name')
                        ->getQuery()
                        ->getResult()
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function stemcellsAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Deployment:stemcells.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['stemcell'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('VeneerBoshBundle:DeploymentsStemcells')
                        ->createQueryBuilder('ds')
                        ->join('ds.stemcell', 's')->addSelect('s')
                        ->where(new Expr\Comparison('ds.deployment', '=', ':deployment'))->setParameter('deployment', $_bosh['deployment'])
                        ->orderBy('s.name')
                        ->addOrderBy('s.version')
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

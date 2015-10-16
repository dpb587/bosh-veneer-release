<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class DeploymentController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, array $_bosh)
    {
        return $nav->add(
            $_bosh['deployment']['name'],
            [
                'veneer_bosh_deployment_summary' => [
                    'deployment' => $_bosh['deployment']['name'],
                ],
            ],
            [
                'glyphicon' => 'th',
                'expanded' => true,
            ]
        );
    }

    public function summaryAction($_bosh)
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

    public function manifestAction($_bosh)
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
    
    public function releasesAction($_bosh)
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
    
    public function stemcellsAction($_bosh)
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

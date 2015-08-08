<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentController extends AbstractController
{
    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:Deployment:summary.html.twig',
            [
                'result' => $_context['deployment'],
            ],
            [
                'manifest' => $this->generateUrl(
                    'bosh_core_deployment_manifest',
                    [
                        'deployment' => $_context['deployment']['name'],
                    ]
                ),
                'releases' => $this->generateUrl(
                    'bosh_core_deployment_releases',
                    [
                        'deployment' => $_context['deployment']['name'],
                    ]
                ),
                'stemcells' => $this->generateUrl(
                    'bosh_core_deployment_stemcells',
                    [
                        'deployment' => $_context['deployment']['name'],
                    ]
                ),
                'instanceALL' => $this->generateUrl(
                    'bosh_core_deployment_instanceALL_index',
                    [
                        'deployment' => $_context['deployment']['name'],
                    ]
                ),
                'vmALL' => $this->generateUrl(
                    'bosh_core_deployment_vmALL_index',
                    [
                        'deployment' => $_context['deployment']['name'],
                    ]
                ),
            ]
        );
    }

    public function manifestAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:Deployment:manifest.html.twig',
            [
                'string' => $_context['deployment']['manifest'],
            ]
        );
    }
    
    public function releasesAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:Deployment:releases.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['releaseVersion']
                            ->setSerializationHint('release', true)
                            ;
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:DeploymentsReleaseVersions')
                        ->createQueryBuilder('drv')
                        ->join('drv.releaseVersion', 'rv')->addSelect('rv')
                        ->join('rv.release', 'r')->addSelect('r')
                        ->where(new Expr\Comparison('drv.deployment', '=', ':deployment'))->setParameter('deployment', $_context['deployment'])
                        ->orderBy('r.name')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
    
    public function stemcellsAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:Deployment:stemcells.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['stemcell'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:DeploymentsStemcells')
                        ->createQueryBuilder('ds')
                        ->join('ds.stemcell', 's')->addSelect('s')
                        ->where(new Expr\Comparison('ds.deployment', '=', ':deployment'))->setParameter('deployment', $_context['deployment'])
                        ->orderBy('s.name')
                        ->addOrderBy('s.version')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
}

<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;

class DeploymentController extends AbstractDeploymentController
{
    public function indexAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:Deployment:index.html.twig',
            $context,
            [
                'result' => $context['deployment'],
            ],
            [
                'manifest' => $this->generateUrl(
                    'bosh_core_deployment_manifest',
                    [
                        'deployment' => $context['deployment']['name'],
                    ]
                ),
                'releases' => $this->generateUrl(
                    'bosh_core_deployment_releases',
                    [
                        'deployment' => $context['deployment']['name'],
                    ]
                ),
                'stemcells' => $this->generateUrl(
                    'bosh_core_deployment_stemcells',
                    [
                        'deployment' => $context['deployment']['name'],
                    ]
                ),
                'instanceALL' => $this->generateUrl(
                    'bosh_core_deployment_instanceALL_index',
                    [
                        'deployment' => $context['deployment']['name'],
                    ]
                ),
                'vmALL' => $this->generateUrl(
                    'bosh_core_deployment_vmALL_index',
                    [
                        'deployment' => $context['deployment']['name'],
                    ]
                ),
            ]
        );
    }

    public function manifestAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:Deployment:manifest.html.twig',
            $context,
            [
                'string' => $context['deployment']['manifest'],
            ]
        );
    }
    
    public function releasesAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:Deployment:releases.html.twig',
            $context,
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
                        ->where(new Expr\Comparison('drv.deployment', '=', ':deployment'))->setParameter('deployment', $context['deployment'])
                        ->orderBy('r.name')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
    
    public function stemcellsAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:Deployment:stemcells.html.twig',
            $context,
            [
                'results' => array_map(
                    function ($v) {
                        return $v['stemcell'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:DeploymentsStemcells')
                        ->createQueryBuilder('ds')
                        ->join('ds.stemcell', 's')->addSelect('s')
                        ->where(new Expr\Comparison('ds.deployment', '=', ':deployment'))->setParameter('deployment', $context['deployment'])
                        ->orderBy('s.name')
                        ->addOrderBy('s.version')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
}

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
                'data' => $_context['deployment'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/deployment', $_context),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/deployment', $_context),
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

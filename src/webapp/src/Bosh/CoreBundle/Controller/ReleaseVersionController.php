<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;

class ReleaseVersionController extends AbstractReleaseVersionController
{
    public function indexAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:ReleaseVersion:index.html.twig',
            $context,
            [
                'version' => $context['version'],
            ],
            [
                'deployments' => $this->generateUrl(
                    'bosh_core_release_version_deployments',
                    [
                        'release' => $context['release']['name'],
                        'version' => $context['version']['version'],
                    ]
                ),
                'packages' => $this->generateUrl(
                    'bosh_core_release_version_packages',
                    [
                        'release' => $context['release']['name'],
                        'version' => $context['version']['version'],
                    ]
                ),
                'templates' => $this->generateUrl(
                    'bosh_core_release_version_templates',
                    [
                        'release' => $context['release']['name'],
                        'version' => $context['version']['version'],
                    ]
                ),
            ]
        );
    }
    
    public function packagesAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:ReleaseVersion:packages.html.twig',
            $context,
            [
                'results' => array_map(
                    function ($v) {
                        return $v['package'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:PackagesReleaseVersions')
                        ->createQueryBuilder('prv')
                        ->join('prv.package', 'p')->addSelect('p')
                        ->where(new Expr\Comparison('prv.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $context['version'])
                        ->orderBy('p.name')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
    
    public function deploymentsAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:ReleaseVersion:deployments.html.twig',
            $context,
            [
                'results' => array_map(
                    function ($v) {
                        return $v['deployment'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:DeploymentsReleaseVersions')
                        ->createQueryBuilder('drv')
                        ->join('drv.deployment', 'd')->addSelect('d')
                        ->where(new Expr\Comparison('drv.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $context['version'])
                        ->orderBy('d.name')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
    
    public function templatesAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:ReleaseVersion:templates.html.twig',
            $context,
            [
                'results' => array_map(
                    function ($v) {
                        return $v['template'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:ReleaseVersionsTemplates')
                        ->createQueryBuilder('rvt')
                        ->join('rvt.template', 't')->addSelect('t')
                        ->where(new Expr\Comparison('rvt.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $context['version'])
                        ->orderBy('t.name')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
}

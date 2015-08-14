<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Bosh\WebBundle\Controller\AbstractController;
use Bosh\WebBundle\Service\Breadcrumbs;

class ReleaseVersionController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_context)
    {
        return ReleaseController::defNav($nav, $_context)
            ->add(
                $_context['version']['version'],
                [
                    'bosh_core_release_version_summary' => [
                        'release' => $_context['release']['name'],
                        'version' => $_context['version']['version'],
                    ],
                ],
                [
                    'glyphicon' => 'record',
                    'expanded' => true,
                ]
            );
    }

    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:ReleaseVersion:summary.html.twig',
            [
                'data' => $_context['version'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/release/version', $_context),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/release/version', $_context),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
    
    public function packagesAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:ReleaseVersion:packages.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['package'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:PackagesReleaseVersions')
                        ->createQueryBuilder('prv')
                        ->join('prv.package', 'p')->addSelect('p')
                        ->where(new Expr\Comparison('prv.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $_context['version'])
                        ->orderBy('p.name')
                        ->getQuery()
                        ->getResult()
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
    
    public function deploymentsAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:ReleaseVersion:deployments.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['deployment'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:DeploymentsReleaseVersions')
                        ->createQueryBuilder('drv')
                        ->join('drv.deployment', 'd')->addSelect('d')
                        ->where(new Expr\Comparison('drv.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $_context['version'])
                        ->orderBy('d.name')
                        ->getQuery()
                        ->getResult()
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
    
    public function templatesAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:ReleaseVersion:templates.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['template'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:ReleaseVersionsTemplates')
                        ->createQueryBuilder('rvt')
                        ->join('rvt.template', 't')->addSelect('t')
                        ->where(new Expr\Comparison('rvt.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $_context['version'])
                        ->orderBy('t.name')
                        ->getQuery()
                        ->getResult()
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
}

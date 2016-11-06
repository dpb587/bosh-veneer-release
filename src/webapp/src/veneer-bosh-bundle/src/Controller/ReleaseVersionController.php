<?php

namespace Veneer\BoshBundle\Controller;

use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;

class ReleaseVersionController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return ReleaseVersionALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['version']['version'],
                [
                    'veneer_bosh_release_version_summary' => [
                        'release' => $_bosh['release']['name'],
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
            'VeneerBoshBundle:ReleaseVersion:summary.html.twig',
            [
                'data' => $_bosh['version'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function packagesAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseVersion:packages.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['package'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('VeneerBoshBundle:PackagesReleaseVersions')
                        ->createQueryBuilder('prv')
                        ->join('prv.package', 'p')->addSelect('p')
                        ->where(new Expr\Comparison('prv.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $_bosh['version'])
                        ->orderBy('p.name')
                        ->getQuery()
                        ->getResult()
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function deploymentsAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseVersion:deployments.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v['deployment'];
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('VeneerBoshBundle:DeploymentsReleaseVersions')
                        ->createQueryBuilder('drv')
                        ->join('drv.deployment', 'd')->addSelect('d')
                        ->where(new Expr\Comparison('drv.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $_bosh['version'])
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

    public function templatesAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseVersion:templates.html.twig',
            [
                'results' => $this->loadTemplates($_bosh),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function propertiesAction($_bosh)
    {
        $specs = [];

        foreach ($this->loadTemplates($_bosh) as $template) {
            $specs[$template['name']] = $template['propertiesJsonAsArray'];
        }

        $mergedSpec = DeploymentPropertySpecHelper::mergeSpecs($specs);

        $propertiesTree = DeploymentPropertySpecHelper::convertSpecToTree($mergedSpec);

        return $this->renderApi(
            'VeneerBoshBundle:ReleaseVersion:properties.html.twig',
            [
                'properties_tree' => $propertiesTree,
                'yaml' => DeploymentPropertySpecHelper::createDocumentedYaml($propertiesTree),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    protected function loadTemplates(array $_bosh)
    {
        return array_map(
            function ($v) {
                return $v['template'];
            },
            $this->container->get('doctrine.orm.bosh_entity_manager')
                ->getRepository('VeneerBoshBundle:ReleaseVersionsTemplates')
                ->createQueryBuilder('rvt')
                ->join('rvt.template', 't')->addSelect('t')
                ->where(new Expr\Comparison('rvt.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $_bosh['version'])
                ->orderBy('t.name')
                ->getQuery()
                ->getResult()
        );
    }
}

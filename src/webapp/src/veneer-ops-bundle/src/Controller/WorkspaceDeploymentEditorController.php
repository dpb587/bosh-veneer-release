<?php

namespace Veneer\OpsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Controller\WorkspaceRepoController;
use Symfony\Component\Yaml\Yaml;
use Veneer\OpsBundle\Service\DeploymentFormHelper;
use Veneer\BoshBundle\Controller\DeploymentController;
use Veneer\BoshBundle\Entity\Deployments;
use Doctrine\ORM\Query\Expr;

class WorkspaceDeploymentEditorController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, $path, $name)
    {
        $mock = new Deployments();
        $refl = new \ReflectionProperty($mock, 'name');
        $refl->setAccessible(true);
        $refl->setValue($mock, $name);

        return DeploymentController::defNav($nav, [ 'deployment' => $mock ])
            ->add(
                'editor',
                [
                    'veneer_ops_workspace_deploymenteditor_summary' => [
                        'path' => $path,
                    ],
                ],
                [
                    'fontawesome' => 'pencil',
                ]
            )
        ;
    }

    public function summaryAction(Request $request)
    {
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($path));

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceDeploymentEditor:summary.html.twig',
            [
                'path' => $path,
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path, $yaml['name']),
                'sidenav_active' => 'summary',
            ]
        );
    }

    public function sectionAction(Request $request, $section)
    {
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($path));
        $tplExtras = [];

        if ('properties' == $section) {
            $propertySets = [];
            $propertyHelper = $this->container->get('veneer_bosh.property_helper');
            $er = $this->container->get('doctrine.orm.bosh_entity_manager')->getRepository('VeneerBoshBundle:ReleaseVersionsTemplates');

            $releaseVersions = [];

            foreach ($yaml['releases'] as $release) {
                $releaseVersions[$release['name']] = $release['version'];
            }

            foreach ($yaml['jobs'] as $job) {
                foreach ($job['templates'] as $template) {
                    $found = $er->createQueryBuilder('rvt')
                        ->addSelect('t')
                        ->join('rvt.releaseVersion', 'rv')
                        ->join('rvt.template', 't')
                        ->join('rv.release', 'r')
                        ->andWhere(new Expr\Comparison('r.name', '=', ':release'))->setParameter('release', $template['release'])
                        ->andWhere(new Expr\Comparison('rv.version', '=', ':version'))->setParameter('version', $releaseVersions[$template['release']])
                        ->andWhere(new Expr\Comparison('t.name', '=', ':name'))->setParameter('name', $template['name'])
                        ->getQuery()
                        ->getSingleResult()
                        ;

                    if (!$found) {
                        throw new \LogicException('Failed to find template for ' . json_encode($template));
                    }

                    $propertySets[$template['name']] = $found['template']['propertiesJsonAsArray'];
                }
            }

            $merged = $propertyHelper->mergePropertySets($propertySets);
            $propertyTree = $propertyHelper->createPropertyTree($merged);

            $tplExtras['properties_tree'] = $propertyTree;
        }

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceDeploymentEditor:section-' . $section . '.html.twig',
            array_merge(
                [
                    'path' => $path,
                    'manifest' => $yaml,
                ],
                $tplExtras
            ),
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path, $yaml['name'])
                    ->add(
                        $section,
                        [
                            'veneer_ops_workspace_deploymenteditor_section' => [
                                'section' => $section,
                                'path' => $path,
                            ],
                        ]
                    ),
                'sidenav_active' => $section,
            ]
        );
    }

    public function editAction(Request $request)
    {
        $path = $request->query->get('path');
        $property = $request->query->get('property');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($path));

        $editor = new DeploymentFormHelper($this->container->get('form.factory'), $yaml);
        $editorProfile = $editor->lookup($property);

        $section = str_replace('_', '', preg_replace('/^([^\.\[]+)(.*)$/', '$1', $property));
        $nav = self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path, $yaml['name']);

        if (in_array($section, [ 'compilation', 'update' ])) {
            $nav->add($section);
        } else {
            $nav->add(
                $section,
                [
                    'veneer_ops_workspace_deploymenteditor_section' => [
                        'section' => $section,
                        'path' => $path,
                    ],
                ]
            );
        }

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceDeploymentEditor:edit.html.twig',
            [
                'path' => $path,
                'manifest' => $yaml,
                'form' => $editorProfile['form']->createView(),
            ],
            [
                'def_nav' => $nav,
                'sidenav_active' => $section,
            ]
        );
    }
}

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
use Symfony\Component\PropertyAccess\PropertyAccess;

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
            $propertyHelper = $this->container->get('veneer_bosh.property_helper');

            // aaaaaa

            $merged = $propertyHelper->mergeManifestPropertySets($yaml);
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

        $editor = new DeploymentFormHelper($this->container->get('form.factory'), $this->container->get('veneer_bosh.property_helper'));
        $editorProfile = $editor->lookup($yaml, $property);

        if ($request->request->has($editorProfile['form']->getName())) {
            $editorProfile['form']->bind($request);

            if ($editorProfile['form']->isValid()) {
                $accessor = PropertyAccess::createPropertyAccessor();

                $accessor->setValue($yaml, $editorProfile['path'], $editorProfile['form']->getData());

                die(print_r($yaml, true));
            }
        }

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
                'property' => $property,
                'title' => $editorProfile['title'],
                'form' => $editorProfile['form']->createView(),
            ],
            [
                'def_nav' => $nav,
                'sidenav_active' => $section,
            ]
        );
    }
}

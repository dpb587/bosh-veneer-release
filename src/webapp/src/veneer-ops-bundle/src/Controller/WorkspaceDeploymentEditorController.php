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

class WorkspaceDeploymentEditorController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, $path)
    {
        return $nav
            ->add(
                'Deployment Editor',
                null,
                [
                    'fontawesome' => 'sitemap',
                ]
            )
            ->add(
                $path,
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
                'def_nav' => self::defNav($this->container->get('veneer_ops.breadcrumbs'), $path),
                'sidenav_active' => 'summary',
            ]
        );
    }

    public function sectionAction(Request $request, $section)
    {
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($path));

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceDeploymentEditor:section-' . $section . '.html.twig',
            [
                'path' => $path,
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_ops.breadcrumbs'), $path)
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
        $nav = self::defNav($this->container->get('veneer_ops.breadcrumbs'), $path);

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

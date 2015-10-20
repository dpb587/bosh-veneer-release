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
    public function defNav(Breadcrumbs $nav, $file)
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
                $file,
                [
                    'veneer_ops_workspace_deploymenteditor_summary' => [
                        'file' => $file,
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
        $file = $request->query->get('file');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($file));

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceDeploymentEditor:summary.html.twig',
            [
                'file' => $file,
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_ops.breadcrumbs'), $file),
            ]
        );
    }

    public function sectionAction(Request $request, $section)
    {
        $file = $request->query->get('file');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($file));

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceDeploymentEditor:section-' . $section . '.html.twig',
            [
                'file' => $file,
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_ops.breadcrumbs'), $file)
                    ->add(
                        $section,
                        [
                            'veneer_ops_workspace_deploymenteditor_section' => [
                                'section' => $section,
                                'file' => $file,
                            ],
                        ]
                    ),
                'sidenav_active' => $section,
            ]
        );
    }

    public function editAction(Request $request)
    {
        $file = $request->query->get('file');
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($file));

        $editor = new DeploymentFormHelper($this->container->get('form.factory'), $yaml);
        $editorProfile = $editor->lookup($path);

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceDeploymentEditor:edit.html.twig',
            [
                'file' => $file,
                'manifest' => $yaml,
                'form' => $editorProfile['form']->createView(),
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_ops.breadcrumbs'), $file),
                'sidenav_active' => str_replace('_', '', preg_replace('/^([^\.\[]+)(.+)$/', '$1', $path)),
            ]
        );
    }
}

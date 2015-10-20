<?php

namespace Veneer\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkspaceRepoController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $path)
    {
        return self::defNavPath(
            $nav
                ->add(
                    'core',
                    [
                        'veneer_core_summary' => [],
                    ]
                )
                ->add(
                    'Workspace',
                    [
                        'veneer_core_workspace_repo_tree' => [
                            'path' => '',
                        ],
                    ],
                    [
                        'fontawesome' => 'folder-open-o',
                    ]
                ),
            $path
        );
    }

    public static function defNavPath(Breadcrumbs $nav, $path)
    {
        $paths = explode('/', trim($path, '/'));

        if ((1 < count($paths)) || ('' != $paths[0])) {
            $partialPath = [];
            foreach ($paths as $path) {
                $partialPath[] = $path;
                $nav->add(
                    $path,
                    [
                        'veneer_core_workspace_repo_tree' => [
                            'path' => implode('/', $partialPath),
                        ],
                    ]
                );
            }
        }

        return $nav;
    }

    public function treeAction($path)
    {
        $repo = $this->container->get('veneer_core.workspace.repository');

        return $this->renderApi(
            'VeneerCoreBundle:WorkspaceRepo:tree.html.twig',
            [
                'path' => $path,
                'children' => $repo->listDirectory($path),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_core.breadcrumbs'), $path),
            ]
        );
    }

    public function editorAction($path)
    {
        $repo = $this->container->get('veneer_core.workspace.repository');

        try {
            $editor = $this->container->get('veneer_core.workspace.editor')->findEditor($path);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('No editor available', $e);
        }

        return $this->redirectToRoute(
            $editor->getRoute(),
            [
                'path' => $path,
            ]
        );
    }
}

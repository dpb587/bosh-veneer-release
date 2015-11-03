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
    public static function defNav(Breadcrumbs $nav, $ref, $path)
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
                        'veneer_core_workspace_repo_tree_index' => [
                            'ref' => $ref,
                            'path' => '',
                        ],
                    ],
                    [
                        'fontawesome' => 'folder-open-o',
                    ]
                ),
            $ref,
            $path
        );
    }

    public static function defNavPath(Breadcrumbs $nav, $ref, $path)
    {
        $paths = explode('/', trim($path, '/'));

        if ((1 < count($paths)) || ('' != $paths[0])) {
            $partialPath = [];
            foreach ($paths as $path) {
                $partialPath[] = $path;
                $nav->add(
                    $path,
                    [
                        'veneer_core_workspace_repo_tree_index' => [
                            'ref' => $ref,
                            'path' => implode('/', $partialPath),
                        ],
                    ]
                );
            }
        }

        return $nav;
    }

    public function appAction($path)
    {
        try {
            $app = $this->container->get('veneer_core.workspace.app')->findApp($path);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('No app available', $e);
        }

        return $this->redirectToRoute(
            $app->getAppRoute(),
            [
                'path' => $path,
            ]
        );
    }
}

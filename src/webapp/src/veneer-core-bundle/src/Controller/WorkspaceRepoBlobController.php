<?php

namespace Veneer\CoreBundle\Controller;

use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkspaceRepoBlobController extends AbstractController
{
    public function indexAction($ref, $path)
    {
        $path = ltrim($path, '/');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $checkout = $repo->createCheckout($ref);

        if (!$checkout->exists($path)) {
            throw new NotFoundHttpException('File not found');
        }

        $apps = $this->container->get('veneer_core.workspace.app');

        try {
            $app = $apps->findApp($path);
        } catch (\Exception $e) {
        }

        return $this->renderApi(
            'VeneerCoreBundle:WorkspaceRepoBlob:index.html.twig',
            [
                'ref' => $ref,
                'path' => $path,
                'data' => $checkout->get($path),
            ],
            [
                'def_nav' => WorkspaceRepoController::defNav($this->container->get('veneer_core.breadcrumbs'), $ref, $path),
                'workspace_app' => $app,
            ]
        );
    }
}

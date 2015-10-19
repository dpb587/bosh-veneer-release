<?php

namespace Veneer\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Controller\WorkspaceRepoController;

class WorkspaceRepoEditorController extends AbstractController
{
    public function indexAction($path)
    {
        $repo = $this->container->get('veneer_core.workspace.repository');

        return $this->renderApi(
            'VeneerCoreBundle:WorkspaceRepo:tree.html.twig',
            [
                'path' => $path,
                'children' => $repo->listDirectory($path),
            ],
            [
                'def_nav' => WorkspaceRepoController::defNav($this->container->get('veneer_core.breadcrumbs'), $path),
            ]
        );
    }
}

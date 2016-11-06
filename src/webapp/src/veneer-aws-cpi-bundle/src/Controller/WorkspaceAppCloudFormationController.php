<?php

namespace Veneer\AwsCpiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class WorkspaceAppCloudFormationController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, $path)
    {
        return $nav
            ->add(
                'editor',
                [
                    'veneer_awscpi_workspace_app_cloudformation_summary' => [
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
        $template = json_decode($repo->showFile($path), true);

        return $this->renderApi(
            'VeneerAwsCpiBundle:WorkspaceAppCloudFormation:summary.html.twig',
            [
                'path' => $path,
                'template' => $template,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path),
                'sidenav_active' => 'summary',
            ]
        );
    }
}

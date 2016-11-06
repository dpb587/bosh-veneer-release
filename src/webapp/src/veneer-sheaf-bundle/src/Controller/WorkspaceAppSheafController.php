<?php

namespace Veneer\SheafBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;

class WorkspaceAppSheafController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, $path)
    {
        return CloudConfigController::defNav($nav)
            ->add(
                'editor',
                [
                    'veneer_ops_workspace_app_cloudconfig_summary' => [
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
        $draftProfile = $repo->getDraftProfile('sheaf-install-'.substr(md5($path), 0, 8), $path);

        $yaml = $this->loadData($repo, $path, $draftProfile);

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppCloudConfig:summary.html.twig',
            [
                'draft_profile' => $draftProfile,
                'path' => $path,
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path),
                'sidenav_active' => 'summary',
            ]
        );
    }

    protected function loadData(RepositoryInterface $repo, $path, array $draftProfile)
    {
        if ($repo->fileExists($path, $draftProfile['ref_read'])) {
            return Yaml::parse($repo->showFile($path, $draftProfile['ref_read'])) ?: [];
        }

        return null;
    }
}
<?php

namespace Veneer\AwsCpiBundle\Controller;

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
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;

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

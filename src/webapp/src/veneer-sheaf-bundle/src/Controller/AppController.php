<?php

namespace Veneer\SheafBundle\Controller;

use Elastica\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;

class AppController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, array $installation, $path)
    {
        return $nav->add(
                $installation['installation']['name'],
                [
                    'veneer_sheaf_app_summary' => [
                        'path' => $path,
                    ],
                ]
            )
            ;
    }

    public function summaryAction(Request $request)
    {
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $draftProfile = $repo->getDraftProfile('sheaf-install-'.substr(md5($path), 0, 8), $path);

        $installation = $this->loadData($repo, $path, $draftProfile);

        return $this->renderApi(
            'VeneerSheafBundle:App:summary.html.twig',
            [
                'installation' => $installation,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $installation, $path),
                'sidenav_active' => 'summary',
                'draft_profile' => $draftProfile,
                'path' => $path,
            ]
        );
    }

    public function dependenciesAction(Request $request)
    {
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $draftProfile = $repo->getDraftProfile('sheaf-install-'.substr(md5($path), 0, 8), $path);

        $installation = $this->loadData($repo, $path, $draftProfile);

        $dependencies = $this->container->get('veneer_sheaf.installation_helper')->enumerateBoshDependencies($draftProfile, $path);

        return $this->renderApi(
            'VeneerSheafBundle:App:dependencies.html.twig',
            [
                'dependencies' => $dependencies,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $installation, $path),
                'sidenav_active' => 'dependencies',
                'installation' => $installation,
                'draft_profile' => $draftProfile,
                'path' => $path,
            ]
        );
    }

    public function dependenciesInstallAction(Request $request)
    {
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $draftProfile = $repo->getDraftProfile('sheaf-install-'.substr(md5($path), 0, 8), $path);

        $dependencies = $this->container->get('veneer_sheaf.installation_helper')->enumerateBoshDependencies($draftProfile, $path);

        if ($request->query->get('install') == 'release') {
            $release = $request->query->get('name');

            if (!isset($dependencies['releases'][$release])) {
                throw new NotFoundException('Invalid release');
            } elseif (!isset($dependencies['releases'][$release]['url'])) {
                throw new BadRequestHttpException('Remote download not supported');
            }

            $task = $this->container->get('veneer_bosh.api')->postForTaskId(
                'releases',
                [
                    'location' => $dependencies['releases'][$release]['url'],
                    'sha1' => isset($dependencies['releases'][$release]['sha1']) ? $dependencies['releases'][$release]['sha1'] : null,
                ]
            );

            return $this->redirectToRoute(
                'veneer_bosh_task_summary',
                [
                    'task' => $task,
                    'continue' => $this->container->get('router')->generate(
                        'veneer_sheaf_app_dependencies',
                        [
                            'path' => $path,
                        ]
                    ),
                ]
            );
        }
    }

    protected function loadData(RepositoryInterface $repo, $path, array $draftProfile)
    {
        if ($repo->fileExists($path, $draftProfile['ref_read'])) {
            return Yaml::parse($repo->showFile($path, $draftProfile['ref_read'])) ?: [];
        }

        return null;
    }
}

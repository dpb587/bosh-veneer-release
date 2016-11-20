<?php

namespace Veneer\SheafBundle\Controller;

use Elastica\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\CoreBundle\Plugin\RequestContext\Annotations as CoreContext;

/**
 * @CoreContext\AppPath(name = "sheaf-install")
 * @CoreContext\ControllerMethod
 */
class AppController extends AbstractAppController
{
    public function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return $nav->add(
                $this->installationHash['installation']['name'],
                [
                    'veneer_sheaf_app_summary' => [
                        'path' => $_bosh['app']['path'],
                    ],
                ]
            )
            ;
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerSheafBundle:App:summary.html.twig',
            [
                'installation' => $this->installationHash,
                'logo' => base64_encode($this->container->get('veneer_core.workspace.repository')->showFile(dirname($_bosh['app']['path']).'/logo.png', $_bosh['app']['profile']['ref_read'])),
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $_bosh),
                'sidenav_active' => 'summary',
            ]
        );
    }

    public function dependenciesAction(Context $_bosh)
    {
        $dependencies = $this->installationHelper->enumerateBoshDependencies($_bosh['app']['profile'], $_bosh['app']['path']);

        return $this->renderApi(
            'VeneerSheafBundle:App:dependencies.html.twig',
            [
                'dependencies' => $dependencies,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $_bosh),
                'sidenav_active' => 'dependencies',
                'installation' => $this->installationHash,
            ]
        );
    }

    public function dependenciesInstallAction(Request $request, Context $_bosh)
    {
        $dependencies = $this->installationHelper->enumerateBoshDependencies($_bosh['app']['profile'], $_bosh['app']['path']);

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
                            'path' => $_bosh['app']['path'],
                        ]
                    ),
                ]
            );
        }
    }
}

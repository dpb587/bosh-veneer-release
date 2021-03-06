<?php

namespace Veneer\HubBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\HubBundle\Plugin\RequestContext\Annotations as HubContext;

/**
 * @HubContext\HubReleaseVersion
 */
class HubReleaseVersionController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return HubReleaseVersionALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['version']->getVersion(),
                [
                    'veneer_hub_hub_release_version_summary' => [
                        'hub' => $_bosh['hub']['name'],
                        'release' => $_bosh['release']['name'],
                        'version' => $_bosh['version']->getVersion(),
                    ],
                ]
            );
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerHubBundle:HubReleaseVersion:summary.html.twig',
            [
                'data' => $_bosh['version'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_hub.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function uploadAction(Request $request, Context $_bosh)
    {
        if (Request::METHOD_POST == $request->getMethod()) {
            $authenticatedUrl = $this->container->get('veneer_hub.hubs')
                ->get($_bosh['hub']['name'])
                ->authenticateReleaseTarballUrl($_bosh['version']->getTarballUrl());

            $task = $this->container->get('veneer_bosh.api')->postForTaskId(
                'releases',
                [
                    'location' => $authenticatedUrl,
                ]
            );

            return $this->redirectToRoute(
                'veneer_bosh_task_summary',
                [
                    'task' => $task,
                ]
            );
        }

        return $this->renderApi(
            'VeneerHubBundle:HubReleaseVersion:upload.html.twig',
            [
                'data' => $_bosh['version'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_hub.breadcrumbs'), $_bosh)
                    ->add(
                        'Upload',
                        [
                            'veneer_hub_hub_release_version_upload' => [
                                'hub' => $_bosh['hub']['name'],
                                'release' => $_bosh['release']['name'],
                                'version' => $_bosh['version']->getVersion(),
                            ],
                        ]
                    ),
            ]
        );
    }
}

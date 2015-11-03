<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceReleaseVersionController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return MarketplaceReleaseVersionALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['version']->getVersion(),
                [
                    'veneer_marketplace_marketplace_release_version_summary' => [
                        'marketplace' => $_bosh['marketplace']['name'],
                        'release' => $_bosh['release']['name'],
                        'version' => $_bosh['version']->getVersion(),
                    ],
                ]
            );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerMarketplaceBundle:MarketplaceReleaseVersion:summary.html.twig',
            [
                'data' => $_bosh['version'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_marketplace.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function uploadAction(Request $request, $_bosh)
    {
        if (Request::METHOD_POST == $request->getMethod()) {
            $authenticatedUrl = $this->container->get('veneer_marketplace.marketplaces')
                ->get($_bosh['marketplace']['name'])
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
            'VeneerMarketplaceBundle:MarketplaceReleaseVersion:upload.html.twig',
            [
                'data' => $_bosh['version'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_marketplace.breadcrumbs'), $_bosh)
                    ->add(
                        'Upload',
                        [
                            'veneer_marketplace_marketplace_release_version_upload' => [
                                'marketplace' => $_bosh['marketplace']['name'],
                                'release' => $_bosh['release']['name'],
                                'version' => $_bosh['version']->getVersion(),
                            ],
                        ]
                    ),
            ]
        );
    }
}

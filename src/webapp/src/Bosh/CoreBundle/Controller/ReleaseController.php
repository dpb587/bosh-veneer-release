<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;
use Bosh\WebBundle\Service\Breadcrumbs;

class ReleaseController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_context)
    {
        return $nav->add(
            $_context['release']['name'],
            [
                'bosh_core_release_summary' => [
                    'release' => $_context['release']['name'],
                ],
            ],
            [
                'glyphicon' => 'tree-deciduous',
                'expanded' => true,
            ]
        );
    }

    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:Release:summary.html.twig',
            [
                'data' => $_context['release'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/release', $_context),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/release', $_context),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs'), $_context),
            ]
        );
    }
}

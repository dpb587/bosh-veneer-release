<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class ReleaseController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_context)
    {
        return $nav->add(
            $_context['release']['name'],
            [
                'veneer_bosh_release_summary' => [
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
            'VeneerBoshBundle:Release:summary.html.twig',
            [
                'data' => $_context['release'],
                'endpoints' => $this->container->get('veneer_bosh.plugin_factory')->getEndpoints('bosh/release', $_context),
                'references' => $this->container->get('veneer_bosh.plugin_factory')->getUserReferenceLinks('bosh/release', $_context),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_context),
            ]
        );
    }
}

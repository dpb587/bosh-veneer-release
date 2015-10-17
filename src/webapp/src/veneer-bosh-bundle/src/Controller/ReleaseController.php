<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class ReleaseController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return $nav->add(
            $_bosh['release']['name'],
            [
                'veneer_bosh_release_summary' => [
                    'release' => $_bosh['release']['name'],
                ],
            ],
            [
                'glyphicon' => 'tree-deciduous',
                'expanded' => true,
            ]
        );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Release:summary.html.twig',
            [
                'data' => $_bosh['release'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

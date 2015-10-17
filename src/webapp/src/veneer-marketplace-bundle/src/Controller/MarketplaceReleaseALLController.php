<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class MarketplaceReleaseALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{release}',
            [
                'veneer_bosh_releaseALL_index' => [],
            ],
            [
                'glyphicon' => 'tree-deciduous',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Releases')
                    ->findBy([], [ 'name' => 'ASC' ]),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs')),
            ]
        );
    }
}

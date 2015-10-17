<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Veneer\WebBundle\Service\Breadcrumbs;

class MarketplaceStemcellALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{stemcell}',
            [
                'veneer_bosh_stemcellALL_index' => [],
            ],
            [
                'glyphicon' => 'compressed',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'VeneerBoshBundle:StemcellALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Stemcells')
                    ->findBy([], [ 'name' => 'ASC' ]),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs')),
            ]
        );
    }
}

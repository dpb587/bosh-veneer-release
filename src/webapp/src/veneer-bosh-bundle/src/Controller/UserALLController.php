<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class UserALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{user}',
            [
                'veneer_bosh_userALL_index' => [],
            ],
            [
                'glyphicon' => 'user',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'VeneerBoshBundle:UserALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Users')
                    ->findBy([], [ 'username' => 'ASC' ]),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs')),
            ]
        );
    }
}

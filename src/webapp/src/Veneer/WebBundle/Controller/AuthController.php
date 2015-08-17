<?php

namespace Veneer\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\WebBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    public function beginAction(Request $request)
    {
        return $this->renderApi(
            'VeneerWebBundle:Auth:begin.html.twig',
            [],
            [
                'def_nav' => $this->container->get('veneer_web.breadcrumbs'),
            ]
        );
    }
}

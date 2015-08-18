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
        $exception = $request->getSession()->get('_security.last_error');
        $request->getSession()->remove('_security.last_error');

        return $this->renderApi(
            'VeneerWebBundle:Auth:begin.html.twig',
            [
                'exception' => $exception ? $exception->getMessage() : null,
            ],
            [
                'def_nav' => $this->container->get('veneer_web.breadcrumbs'),
            ]
        );
    }
}

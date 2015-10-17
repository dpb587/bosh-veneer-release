<?php

namespace Veneer\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    public function beginAction(Request $request)
    {
        $exception = $request->getSession()->get('_security.last_error');
        $request->getSession()->remove('_security.last_error');

        return $this->renderApi(
            'VeneerCoreBundle:Auth:begin.html.twig',
            [
                'exception' => $exception ? $exception->getMessage() : null,
            ],
            [
                'def_nav' => $this->container->get('veneer_web.breadcrumbs'),
            ]
        );
    }
}

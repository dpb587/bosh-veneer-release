<?php

namespace Veneer\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function summaryAction(Request $request)
    {
        return $this->renderApi(
            'VeneerCoreBundle:Index:summary.html.twig',
            [],
            [
                'def_nav' => $this->container->get('veneer_web.breadcrumbs'),
            ]
        );
    }

    public function aboutAction()
    {
        return $this->renderApi(
            'VeneerCoreBundle:Index:about.html.twig',
            [],
            [
                'def_nav' => $this->container->get('veneer_web.breadcrumbs')
                    ->add('About'),
            ]
        );
    }
}

<?php

namespace Veneer\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\WebBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function summaryAction(Request $request)
    {
        return $this->renderApi(
            'VeneerWebBundle:Index:summary.html.twig',
            [],
            [
                'def_nav' => $this->container->get('veneer_web.breadcrumbs'),
            ]
        );
    }
}

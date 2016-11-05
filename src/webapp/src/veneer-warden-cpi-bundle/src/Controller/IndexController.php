<?php

namespace Veneer\WardenCpiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function summaryAction()
    {
        return $this->renderApi(
            'VeneerWardenCpiBundle:Index:summary.html.twig',
            [],
            [
                'def_nav' => $this->container->get('veneer_warden_cpi.breadcrumbs'),
            ]
        );
    }
}

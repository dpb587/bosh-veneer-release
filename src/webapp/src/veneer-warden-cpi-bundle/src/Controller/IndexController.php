<?php

namespace Veneer\WardenCpiBundle\Controller;

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

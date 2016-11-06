<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class CloudConfigController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            'cloud-config',
            [
                'veneer_bosh_cloudconfig_summary' => [],
            ],
            [
                'fontawesome' => 'cloud',
            ]
        );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:CloudConfig:summary.html.twig',
            [
                'data' => $_bosh['cloudconfig'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function manifestAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:CloudConfig:manifest.html.twig',
            [
                'string' => $_bosh['cloudconfig']['properties'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class ReleaseJobController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return ReleaseJobALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['job']['name'].'/'.$_bosh['job']['version'],
                [
                    'veneer_bosh_release_job_summary' => [
                        'release' => $_bosh['release']['name'],
                        'job' => $_bosh['job']['name'],
                        'version' => $_bosh['job']['version'],
                    ],
                ],
                [
                    'fontawesome' => 'record',
                    'expanded' => true,
                ]
            );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseJob:summary.html.twig',
            [
                'data' => $_bosh['job'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }

    public function propertiesAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseJob:properties.html.twig',
            [
                'properties' => $_bosh['job']['propertiesJsonAsArray'],
            ]
        );
    }
}

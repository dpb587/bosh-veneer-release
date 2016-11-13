<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\Release
 */
class ReleaseController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return ReleaseALLController::defNav($nav)
            ->add(
                $_bosh['release']['name'],
                [
                    'veneer_bosh_release_summary' => [
                        'release' => $_bosh['release']['name'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Release:summary.html.twig',
            [
                'data' => $_bosh['release'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

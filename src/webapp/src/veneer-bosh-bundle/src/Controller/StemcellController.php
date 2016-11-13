<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\Stemcell
 */
class StemcellController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return StemcellALLController::defNav($nav)
            ->add(
                $_bosh['stemcell']['name'],
                [
                    'veneer_bosh_stemcell_summary' => [
                        'stemcell' => $_bosh['stemcell']['name'],
                    ],
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:Stemcell:summary.html.twig',
            [
                'data' => $_bosh['stemcell'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

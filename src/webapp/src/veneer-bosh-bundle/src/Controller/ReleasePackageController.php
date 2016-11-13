<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\ReleasePackage
 */
class ReleasePackageController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return ReleasePackageALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['package']['name'].'/'.$_bosh['package']['version'],
                [
                    'veneer_bosh_release_package_summary' => [
                        'release' => $_bosh['release']['name'],
                        'package' => $_bosh['package']['name'],
                        'version' => $_bosh['package']['version'],
                    ],
                ]
            );
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleasePackage:summary.html.twig',
            [
                'data' => $_bosh['package'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

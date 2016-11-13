<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\DeploymentInstanceGroup
 */
class DeploymentInstanceGroupController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return DeploymentInstanceGroupALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['instance_group']['job'],
                [
                    'veneer_bosh_deployment_instancegroup_summary' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                    ],
                ],
                [
                    'expanded' => true,
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroup:summary.html.twig',
            [
                'data' => $_bosh['instance_group'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\DeploymentInstanceGroupInstance
 */
class DeploymentInstanceGroupInstanceNetworkALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return DeploymentInstanceGroupInstanceController::defNav($nav, $_bosh)
            ->add(
                'network',
                [
                    'veneer_bosh_deployment_instancegroup_instance_networkALL_index' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                        'instance' => $_bosh['instance']['uuid'],
                    ],
                ],
                [
                    'fontawesome' => 'exchange',
                ]
            );
    }

    public function indexAction(Context $_bosh)
    {
        $results = $_bosh['instance']['specJsonAsArray']['networks'];

        foreach ($results as $k => $v) {
            $results[$k]['name'] = $k;
        }

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstanceNetworkALL:index.html.twig',
            [
                'results' => array_values($results),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

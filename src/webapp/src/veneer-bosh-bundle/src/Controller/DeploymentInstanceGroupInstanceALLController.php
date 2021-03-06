<?php

namespace Veneer\BoshBundle\Controller;

use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\DeploymentInstanceGroup
 */
class DeploymentInstanceGroupInstanceALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return DeploymentInstanceGroupController::defNav($nav, $_bosh)
            ->add(
                'instance',
                [
                    'veneer_bosh_deployment_instancegroup_instanceALL_index' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                    ],
                ],
                [
                    'fontawesome' => 'cube',
                ]
            )
        ;
    }

    public function indexAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstanceALL:index.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v
                            ->setSerializationHint('vm', false)
                            ;
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('VeneerBoshBundle:Instances')
                        ->createQueryBuilder('i')
                        ->where(new Expr\Comparison('i.deployment', '=', ':deployment'))->setParameter('deployment', $_bosh['deployment'])
                        ->andWhere(new Expr\Comparison('i.job', '=', ':job'))->setParameter('job', $_bosh['instance_group']['job'])
                        ->orderBy('i.index', 'ASC')
                        ->getQuery()
                        ->getResult()
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

<?php

namespace Veneer\BoshBundle\Controller;

use Veneer\CoreBundle\Controller\AbstractController;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentInstanceGroupInstancePersistentDiskALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentInstanceGroupInstanceController::defNav($nav, $_bosh)
            ->add(
                'persistent disk',
                [
                    'veneer_bosh_deployment_instancegroup_instance_persistentdiskALL_index' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'instance_group' => $_bosh['instance_group']['job'],
                        'instance' => $_bosh['instance']['uuid'],
                    ],
                ],
                [
                    'fontawesome' => 'hdd-o',
                ]
            )
            ;
    }

    public function indexAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupInstancePersistentDiskALL:index.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v
                            ->setSerializationHint('vm', false)
                            ;
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('VeneerBoshBundle:PersistentDisks')
                        ->createQueryBuilder('pd')
                        ->where(new Expr\Comparison('pd.instance', '=', ':instance'))->setParameter('instance', $_bosh['instance'])
                        ->addOrderBy('pd.id', 'ASC')
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

<?php

namespace Veneer\BoshBundle\Controller;

use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\BoshBundle\Plugin\RequestContext\Annotations as BoshContext;

/**
 * @BoshContext\Deployment
 */
class DeploymentInstanceGroupALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return DeploymentController::defNav($nav, $_bosh)
            ->add(
                'instance group',
                [
                    'veneer_bosh_deployment_jobALL_index' => [
                        'deployment' => $_bosh['deployment']['name'],
                    ],
                ],
                [
                    'fontawesome' => 'cubes',
                ]
            )
        ;
    }

    public function indexAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceGroupALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Instances')
                    ->createQueryBuilder('i')
                    ->distinct()
                    ->select('i.job')
                    ->where(new Expr\Comparison('i.deployment', '=', ':deployment'))->setParameter('deployment', $_bosh['deployment'])
                    ->addOrderBy('i.job', 'ASC')
                    ->getQuery()
                    ->getResult(),
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
}

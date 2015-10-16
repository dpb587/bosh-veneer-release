<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\WebBundle\Controller\AbstractController;

class DeploymentInstanceALLController extends AbstractController
{
    public function indexAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstanceALL:index.html.twig',
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
                        ->join('i.vm', 'v')
                        ->where(new Expr\Comparison('i.deployment', '=', ':deployment'))->setParameter('deployment', $_bosh['deployment'])
                        ->addOrderBy('i.job', 'ASC')
                        ->addOrderBy('i.index', 'ASC')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
}

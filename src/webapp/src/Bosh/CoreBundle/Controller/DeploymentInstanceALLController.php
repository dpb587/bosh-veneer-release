<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentInstanceALLController extends AbstractController
{
    public function indexAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentInstanceALL:index.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v
                            ->setSerializationHint('vm', false)
                            ;
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:Instances')
                        ->createQueryBuilder('i')
                        ->join('i.vm', 'v')
                        ->where(new Expr\Comparison('i.deployment', '=', ':deployment'))->setParameter('deployment', $_context['deployment'])
                        ->addOrderBy('i.job', 'ASC')
                        ->addOrderBy('i.index', 'ASC')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
}

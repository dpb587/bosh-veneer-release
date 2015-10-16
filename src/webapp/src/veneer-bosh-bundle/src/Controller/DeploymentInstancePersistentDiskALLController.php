<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;
use Doctrine\ORM\Query\Expr;

class DeploymentInstancePersistentDiskALLController extends AbstractController
{
    public function indexAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:DeploymentInstancePersistentDiskALL:index.html.twig',
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
            ]
        );
    }
}

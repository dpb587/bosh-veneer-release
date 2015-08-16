<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;
use Doctrine\ORM\Query\Expr;

class DeploymentInstancePersistentDiskALLController extends AbstractController
{
    public function indexAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentInstancePersistentDiskALL:index.html.twig',
            [
                'results' => array_map(
                    function ($v) {
                        return $v
                            ->setSerializationHint('vm', false)
                            ;
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:PersistentDisks')
                        ->createQueryBuilder('pd')
                        ->where(new Expr\Comparison('pd.instance', '=', ':instance'))->setParameter('instance', $_context['instance'])
                        ->addOrderBy('pd.id', 'ASC')
                        ->getQuery()
                        ->getResult()
                ),
            ]
        );
    }
}

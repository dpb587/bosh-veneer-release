<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;

class ReleaseTemplateALLController extends AbstractController
{
    public function indexAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseTemplateALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('VeneerBoshBundle:Templates')
                    ->createQueryBuilder('t')
                    ->andWhere(new Expr\Comparison('t.release', '=', ':release'))->setParameter('release', $_bosh['release'])
                    ->addOrderBy('t.name')
                    ->addOrderBy('t.version')
                    ->getQuery()
                    ->getResult(),
            ]
        );
    }
}

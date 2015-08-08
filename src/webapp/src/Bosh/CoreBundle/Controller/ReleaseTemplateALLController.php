<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Bosh\WebBundle\Controller\AbstractController;

class ReleaseTemplateALLController extends AbstractController
{
    public function indexAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:ReleaseTemplateALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('BoshCoreBundle:Templates')
                    ->createQueryBuilder('t')
                    ->andWhere(new Expr\Comparison('t.release', '=', ':release'))->setParameter('release', $_context['release'])
                    ->addOrderBy('t.name')
                    ->addOrderBy('t.version')
                    ->getQuery()
                    ->getResult(),
            ]
        );
    }
}

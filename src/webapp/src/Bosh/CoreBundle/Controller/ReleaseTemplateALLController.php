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
            'BoshCoreBundle:ReleasePackageALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('BoshCoreBundle:Packages')
                    ->createQueryBuilder('p')
                    ->andWhere(new Expr\Comparison('p.release', '=', ':release'))->setParameter('release', $_context['release'])
                    ->addOrderBy('p.name')
                    ->addOrderBy('p.version')
                    ->getQuery()
                    ->getResult(),
            ]
        );
    }
}

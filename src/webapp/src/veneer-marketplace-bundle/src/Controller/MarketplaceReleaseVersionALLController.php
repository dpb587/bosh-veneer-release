<?php

namespace Veneer\MarketplaceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;

class MarketplaceReleaseVersionALLController extends AbstractController
{
    public function indexAction($_bosh)
    {
        $results = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('VeneerBoshBundle:ReleaseVersions')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.release', '=', ':release'))->setParameter('release', $_bosh['release'])
            ->addOrderBy('v.version')
            ->getQuery()
            ->getResult();
        
        usort(
            $results,
            function($a, $b) {
                return -1 * version_compare($a['version'], $b['version']);
            }
        );

        return $this->renderApi(
            'VeneerBoshBundle:ReleaseVersionALL:index.html.twig',
            [
                'results' => $results,
            ]
        );
    }
}

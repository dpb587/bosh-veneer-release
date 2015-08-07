<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;

class ReleaseVersionALLController extends AbstractReleaseController
{
    public function indexAction(Request $request)
    {
        $context = $this->validateRequest($request);

        $results = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('BoshCoreBundle:ReleaseVersions')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.release', '=', ':release'))->setParameter('release', $context['release'])
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
            'BoshCoreBundle:ReleaseVersionALL:index.html.twig',
            $context,
            [
                'results' => $results,
            ]
        );
    }
}

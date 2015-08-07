<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;

abstract class AbstractReleaseController extends AbstractController
{
    protected function validateRequest(Request $request)
    {
        $context = parent::validateRequest($request);

        $release = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('BoshCoreBundle:Releases')
            ->createQueryBuilder('r')
            ->andWhere(new Expr\Comparison('r.name', '=', ':release'))->setParameter('release', $request->attributes->get('release'))
            ->getQuery()
            ->getSingleResult();
        
        if (!$release) {
            throw new NotFoundHttpException();
        }

        $context['release'] = $release;
        
        return $context;
    }
}

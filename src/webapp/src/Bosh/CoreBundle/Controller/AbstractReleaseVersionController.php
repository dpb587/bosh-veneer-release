<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;

abstract class AbstractReleaseVersionController extends AbstractReleaseController
{
    protected function validateRequest(Request $request)
    {
        $context = parent::validateRequest($request);

        $version = $this->container->get('doctrine.orm.bosh_entity_manager')
            ->getRepository('BoshCoreBundle:ReleaseVersions')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.release', '=', ':release'))->setParameter('release', $context['release'])
            ->andWhere(new Expr\Comparison('v.version', '=', ':version'))->setParameter('version', $request->attributes->get('version'))
            ->getQuery()
            ->getSingleResult();
        
        if (!$version) {
            throw new NotFoundHttpException();
        }

        $context['version'] = $version;
        
        return $context;
    }
}

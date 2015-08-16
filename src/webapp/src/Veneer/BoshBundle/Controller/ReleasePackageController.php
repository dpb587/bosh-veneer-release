<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\WebBundle\Controller\AbstractController;

class ReleasePackageController extends AbstractController
{
    public function summaryAction($_context)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleasePackage:summary.html.twig',
            [
                'data' => $_context['package'],
                'endpoints' => $this->container->get('veneer_bosh.plugin_factory')->getEndpoints('bosh/release/package', $_context),
            ]
        );
    }
}

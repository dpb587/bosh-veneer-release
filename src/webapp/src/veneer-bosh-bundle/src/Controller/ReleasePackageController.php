<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;

class ReleasePackageController extends AbstractController
{
    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleasePackage:summary.html.twig',
            [
                'data' => $_bosh['package'],
            ]
        );
    }
}

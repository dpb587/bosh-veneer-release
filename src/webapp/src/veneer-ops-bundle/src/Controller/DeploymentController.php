<?php

namespace Veneer\OpsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Veneer\BoshBundle\Controller\DeploymentController as BaseDeploymentController;

class DeploymentController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, array $_bosh)
    {
        return BaseDeploymentController::defNav($nav, $_bosh)->add(
            'Editor',
            null,
            #[
            #    'veneer_bosheditor_deployment_summary' => [
            #        'deployment' => $_bosh['deployment']['name'],
            #    ],
            #],
            [
                'fontawesome' => 'th',
                'expanded' => true,
            ]
        );
    }
}

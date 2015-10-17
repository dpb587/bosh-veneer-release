<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class ReleaseTemplateController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return ReleaseTemplateALLController::defNav($nav, $_bosh)
            ->add(
                $_bosh['template']['name'] . '/' . $_bosh['template']['version'],
                [
                    'veneer_bosh_release_template_summary' => [
                        'release' => $_bosh['release']['name'],
                        'template' => $_bosh['template']['name'],
                        'version' => $_bosh['template']['version'],
                    ],
                ],
                [
                    'fontawesome' => 'record',
                    'expanded' => true,
                ]
            );
    }

    public function summaryAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseTemplate:summary.html.twig',
            [
                'data' => $_bosh['template'],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
            ]
        );
    }
    
    public function propertiesAction($_bosh)
    {
        return $this->renderApi(
            'VeneerBoshBundle:ReleaseTemplate:properties.html.twig',
            [
                'properties' => $_bosh['template']['propertiesJsonAsArray'],
            ]
        );
    }
}

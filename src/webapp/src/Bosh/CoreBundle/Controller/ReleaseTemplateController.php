<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Bosh\WebBundle\Controller\AbstractController;

class ReleaseTemplateController extends AbstractController
{
    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:ReleaseTemplate:summary.html.twig',
            [
                'data' => $_context['template'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/release/template', $_context),
            ]
        );
    }
    
    public function propertiesAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:ReleaseTemplate:properties.html.twig',
            [
                'properties' => $_context['template']['propertiesJsonAsArray'],
            ]
        );
    }
}

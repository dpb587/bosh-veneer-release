<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class ReleaseController extends AbstractController
{
    public function summaryAction($_context)
    {
        return $this->renderApi(
            'BoshCoreBundle:Release:summary.html.twig',
            [
                'data' => $_context['release'],
                'endpoints' => $this->container->get('bosh_core.plugin_factory')->getEndpoints('bosh/release', $_context),
            ]
        );
    }
}

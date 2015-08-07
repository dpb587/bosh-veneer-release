<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReleaseController extends AbstractReleaseController
{
    public function indexAction(Request $request)
    {
        $context = $this->validateRequest($request);

        return $this->renderApi(
            'BoshCoreBundle:Release:index.html.twig',
            $context,
            [
                'result' => $context['release'],
            ],
            [
                'packageALL' => $this->generateUrl(
                    'bosh_core_release_packageALL_index',
                    [
                        'release' => $context['release']['name'],
                    ]
                ),
                'versionALL' => $this->generateUrl(
                    'bosh_core_release_versionALL_index',
                    [
                        'release' => $context['release']['name'],
                    ]
                ),
            ]
        );
    }
}

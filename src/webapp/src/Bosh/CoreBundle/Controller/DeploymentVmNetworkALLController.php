<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeploymentVmNetworkALLController extends AbstractDeploymentVmController
{
    public function indexAction(Request $request, $_format)
    {
        $context = $this->validateRequest($request);

        $results = $context['vm']['applySpecJsonAsArray']['networks'];
        
        foreach ($results as $k => $v) {
            $results[$k]['name'] = $k;
        }

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVmNetworkALL:index.html.twig',
            $context,
            [
                'results' => array_values($results),
            ]
        );
    }
}

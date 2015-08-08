<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentVmNetworkALLController extends AbstractController
{
    public function indexAction($_context)
    {
        $results = $_context['vm']['applySpecJsonAsArray']['networks'];
        
        foreach ($results as $k => $v) {
            $results[$k]['name'] = $k;
        }

        return $this->renderApi(
            'BoshCoreBundle:DeploymentVmNetworkALL:index.html.twig',
            [
                'results' => array_values($results),
            ]
        );
    }
}

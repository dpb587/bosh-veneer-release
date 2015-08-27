<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\WebBundle\Controller\AbstractController;

class DeploymentVmNetworkALLController extends AbstractController
{
    public function indexAction($_bosh)
    {
        $results = $_bosh['vm']['applySpecJsonAsArray']['networks'];
        
        foreach ($results as $k => $v) {
            $results[$k]['name'] = $k;
        }

        return $this->renderApi(
            'VeneerBoshBundle:DeploymentVmNetworkALL:index.html.twig',
            [
                'results' => array_values($results),
            ]
        );
    }
}

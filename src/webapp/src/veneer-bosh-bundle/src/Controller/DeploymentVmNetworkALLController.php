<?php

namespace Veneer\BoshBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class DeploymentVmNetworkALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav, $_bosh)
    {
        return DeploymentVmController::defNav($nav, $_bosh)
            ->add(
                'networks',
                [
                    'veneer_bosh_deployment_vm_networkALL_index' => [
                        'deployment' => $_bosh['deployment']['name'],
                        'agent' => $_bosh['vm']['agentId'],
                    ],
                ]
            );
    }

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

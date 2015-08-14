<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;
use Bosh\WebBundle\Service\Breadcrumbs;

class DeploymentALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{deployment}',
            [
                'bosh_core_deploymentALL_index' => [],
            ],
            [
                'glyphicon' => 'th',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'BoshCoreBundle:DeploymentALL:index.html.twig',
            [
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/deployment:all'),
                'results' => array_map(
                    function ($v) {
                        return $v
                            ->setSerializationHint('manifest', false)
                            ->setSerializationHint('cloudConfig', false)
                            ;
                    },
                    $this->container->get('doctrine.orm.bosh_entity_manager')
                        ->getRepository('BoshCoreBundle:Deployments')
                        ->findBy([], [ 'name' => 'ASC' ])
                ),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs')),
            ]
        );
    }
}

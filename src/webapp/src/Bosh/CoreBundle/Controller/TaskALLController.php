<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;
use Bosh\WebBundle\Service\Breadcrumbs;

class TaskALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{task}',
            [
                'bosh_core_taskALL_index' => [],
            ],
            [
                'glyphicon' => 'tasks',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'BoshCoreBundle:TaskALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('BoshCoreBundle:Tasks')
                    ->findBy([], [ 'id' => 'DESC' ]),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/task:all'),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs')),
            ]
        );
    }
}

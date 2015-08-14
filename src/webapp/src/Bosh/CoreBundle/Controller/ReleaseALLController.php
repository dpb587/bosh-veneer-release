<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;
use Bosh\WebBundle\Service\Breadcrumbs;

class ReleaseALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{release}',
            [
                'bosh_core_releaseALL_index' => [],
            ],
            [
                'glyphicon' => 'tree-deciduous',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'BoshCoreBundle:ReleaseALL:index.html.twig',
            [
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/release:all'),
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('BoshCoreBundle:Releases')
                    ->findBy([], [ 'name' => 'ASC' ]),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs')),
            ]
        );
    }
}

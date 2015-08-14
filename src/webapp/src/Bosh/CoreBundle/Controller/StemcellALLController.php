<?php

namespace Bosh\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bosh\WebBundle\Controller\AbstractController;
use Bosh\WebBundle\Service\Breadcrumbs;

class StemcellALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            '{stemcell}',
            [
                'bosh_core_stemcellALL_index' => [],
            ],
            [
                'glyphicon' => 'compressed',
            ]
        );
    }

    public function indexAction()
    {
        return $this->renderApi(
            'BoshCoreBundle:StemcellALL:index.html.twig',
            [
                'results' => $this->container->get('doctrine.orm.bosh_entity_manager')
                    ->getRepository('BoshCoreBundle:Stemcells')
                    ->findBy([], [ 'name' => 'ASC' ]),
                'references' => $this->container->get('bosh_core.plugin_factory')->getUserReferenceLinks('bosh/stemcell:all'),
            ],
            [
                'def_nav' => static::defNav($this->container->get('bosh_core.breadcrumbs')),
            ]
        );
    }
}

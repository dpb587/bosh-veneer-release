<?php

namespace Veneer\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Service\SchemaEditor\PrototypeNode;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        return $this->redirectToRoute('veneer_core_summary');
    }

    public function summaryAction(Request $request)
    {
        return $this->renderApi(
            'VeneerCoreBundle:Index:summary.html.twig',
            [],
            [
                'def_nav' => $this->container->get('veneer_core.breadcrumbs'),
            ]
        );
    }

    public function aboutAction()
    {
        return $this->renderApi(
            'VeneerCoreBundle:Index:about.html.twig',
            [],
            [
                'def_nav' => $this->container->get('veneer_core.breadcrumbs')
                    ->add('About'),
            ]
        );
    }

    public function devAction()
    {
//        $manifest;
//        $node = $manifest->traverse('/instance_groups/name=web/jobs/name=atc/properties/postgresql_database');
//
//        $node->getParent()->getParent()->getData() = ['name' => 'atc' ];
//        $node->getEditableContexts() = [ 'all', 'job/name=atc/properties', 'instance_groups/name=web' ];
//        $node->getEditableContext('job/name=atc/properties') = [ 'title' => 'atc Job Properties', 'group' => 'web Instance Group' ];
//        $node->getTitle() = 'Postgresql Database';
//        $node->getDescription() = 'What database should be used?';
//        $node->getForm();
    }
}

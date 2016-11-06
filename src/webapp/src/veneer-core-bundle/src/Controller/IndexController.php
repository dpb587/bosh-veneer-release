<?php

namespace Veneer\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

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
}

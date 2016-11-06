<?php

namespace Veneer\SheafBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\SheafBundle\Entity\Sheaf;

class InstallationALLController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav
            ->add(
                'sheaves',
                [
                    'veneer_sheaf_listingALL_index' => [],
                ]
            )
            ;
    }

    public function indexAction()
    {
        $repo = $this->container->get('veneer_core.workspace.repository');

        $checkout = $repo->createCheckout();

        $results = [];

        foreach ($checkout->ls('sheaf') as $installation) {
            $specPath = 'sheaf/' . $installation['name'] . '/installation.yml';

            if (!$checkout->exists($specPath)) {
                continue;
            }

            $results[] = Yaml::parse($checkout->get($specPath));
        }

        return $this->renderApi(
            'VeneerSheafBundle:InstallationALL:index.html.twig',
            [
                'results' => $results,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs')),
            ]
        );
    }
}

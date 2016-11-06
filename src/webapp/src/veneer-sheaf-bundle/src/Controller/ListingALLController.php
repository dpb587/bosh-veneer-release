<?php

namespace Veneer\SheafBundle\Controller;

use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\SheafBundle\Entity\Sheaf;

class ListingALLController extends AbstractController
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
        $results = $this->container->get('doctrine.orm.state_entity_manager')
            ->getRepository('VeneerSheafBundle:Sheaf')
            ->createQueryBuilder('v')
            ->addOrderBy('v.sheaf', 'ASC')
            ->addOrderBy('v.semverMajor', 'DESC')
            ->addOrderBy('v.semverMinor', 'DESC')
            ->addOrderBy('v.semverPatch', 'DESC')
            ->addOrderBy('v.semverExtra', 'DESC')
            ->addOrderBy('v.version', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->renderApi(
            'VeneerSheafBundle:ListingALL:index.html.twig',
            [
                'results' => $results,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs')),
            ]
        );
    }

    public function importAction(Request $request)
    {
        $formBuilder = $this->container->get('form.factory')->createNamedBuilder('data');
        $formBuilder->add(
            'tarball',
            'url',
            [
                'label' => 'Tarball URL',
                'veneer_help_html' => 'A URL to a valid Sheaf tarball',
            ]
        );
        $form = $formBuilder->getForm();

        if ($request->request->has($form->getName())) {
            $form->bind($request);

            if ($form->isValid()) {
                $listing = $this->container->get('veneer_sheaf.listing_helper')->importTarball($form->get('tarball')->getData());

                return $this->redirectToRoute(
                    'veneer_sheaf_listing_summary',
                    [
                        'listing' => $listing->getId(),
                    ]
                );
            }
        }

        return $this->renderApi(
            'VeneerSheafBundle:ListingALL:import.html.twig',
            [
                'form' => $form->createView(),
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'))
                    ->add('Import', [ 'veneer_sheaf_listingALL_import' => [] ]),
            ]
        );
    }
}

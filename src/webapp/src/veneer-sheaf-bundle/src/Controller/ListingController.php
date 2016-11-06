<?php

namespace Veneer\SheafBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\SheafBundle\Entity\Sheaf;

class ListingController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, Sheaf $sheaf)
    {
        return ListingALLController::defNav($nav)
            ->add(
                $sheaf->getSheaf(),
                [
                    'veneer_sheaf_listing_summary' => [
                        'listing' => $sheaf->getId(),
                    ],
                ]
            )
            ;
    }

    public function summaryAction(Request $request)
    {
        $sheaf = $this->container->get('doctrine.orm.state_entity_manager')->find('VeneerSheafBundle:Sheaf', $request->attributes->get('listing'));

        if (!$sheaf) {
            throw new NotFoundHttpException();
        }

        $sheafPath = $this->container->get('veneer_sheaf.listing_helper')->getStoragePath($sheaf);

        $logo = base64_encode(file_get_contents($sheafPath.'/logo.png'));
        $spec = $this->container->get('veneer_sheaf.listing_helper')->loadFullSpec($sheaf);

        return $this->renderApi(
            'VeneerSheafBundle:Listing:summary.html.twig',
            [
                'sheaf' => $sheaf,
                'logo' => $logo,
                'spec' => $spec,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $sheaf),
            ]
        );
    }

    public function installationsAction(Request $request)
    {
        $sheaf = $this->container->get('doctrine.orm.state_entity_manager')->find('VeneerSheafBundle:Sheaf', $request->attributes->get('listing'));

        if (!$sheaf) {
            throw new NotFoundHttpException();
        }

        return $this->renderApi(
            'VeneerSheafBundle:Listing:installations.html.twig',
            [
                'sheaf' => $sheaf,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $sheaf),
            ]
        );
    }

    public function readmeAction(Request $request)
    {
        $sheaf = $this->container->get('doctrine.orm.state_entity_manager')->find('VeneerSheafBundle:Sheaf', $request->attributes->get('listing'));

        if (!$sheaf) {
            throw new NotFoundHttpException();
        }

        $sheafPath = $this->container->get('veneer_sheaf.listing_helper')->getStoragePath($sheaf);

        return $this->renderApi(
            'VeneerSheafBundle:Listing:readme.html.twig',
            [
                'sheaf' => $sheaf,
                'readme' => file_exists($sheafPath.'/README.md') ? file_get_contents($sheafPath.'/README.md') : null,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $sheaf),
            ]
        );
    }

    public function installAction(Request $request)
    {
        $sheaf = $this->container->get('doctrine.orm.state_entity_manager')->find('VeneerSheafBundle:Sheaf', $request->attributes->get('listing'));

        if (!$sheaf) {
            throw new NotFoundHttpException();
        }

        $sheafPath = $this->container->get('veneer_sheaf.listing_helper')->getStoragePath($sheaf);

        $logo = base64_encode(file_get_contents($sheafPath.'/logo.png'));
        $spec = $this->container->get('veneer_sheaf.listing_helper')->loadFullSpec($sheaf);

        $bulkFormBuilder = $this->container->get('form.factory')->createNamedBuilder('data');
        $bulkFormBuilder->setData([
            'name' => $spec['name'],
        ]);
        $bulkFormBuilder->add(
            'name',
            'text',
            [
                'label' => 'Name',
                'veneer_help_html' => 'This name will become a prefix for all installed components.',
            ]
        );

        $bulkFormBuilder->add('components', 'form');

        foreach ($spec['components'] as $component) {
            $componentForm = $bulkFormBuilder->get('components')->add($component['name'], 'form')->get($component['name']);
            $componentForm->add('features', 'form');

            foreach ($component['features'] as $feature) {
                $choices = [];

                foreach ($feature['choices'] as $choice) {
                    $choices[$choice['name']] = $choice['title'];
                }

                $componentForm->get('features')->add(
                    $feature['name'],
                    'choice',
                    [
                        'choices' => $choices,
                        'expanded' => true,
                        'required' => $feature['required'],
                        'multiple' => $feature['multiple'],
                    ]
                );
            }
        }

        $bulkForm = $bulkFormBuilder->getForm();

        if ($request->request->has($bulkForm->getName())) {
            $bulkForm->bind($request);

            if ($bulkForm->isValid()) {
                $data = $bulkForm->getData();
                $name = $data['name'];

                $path = $this->container->get('veneer_sheaf.listing_helper')->createInstallation(
                    $sheaf,
                    $name,
                    $data,
                    $this->container->get('veneer_core.workspace.repository')
                );

                return $this->redirectToRoute(
                    'veneer_sheaf_app_summary',
                    [
                        'path' => $path,
                    ]
                );
            }
        }

        return $this->renderApi(
            'VeneerSheafBundle:Listing:install.html.twig',
            [
                'sheaf' => $sheaf,
                'logo' => $logo,
                'spec' => $spec,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $sheaf)
                    ->add(
                        'New Installation',
                        [
                            'veneer_sheaf_listing_install' => [
                                'listing' => $sheaf->getId(),
                            ],
                        ]
                    ),
                'form' => $bulkForm->createView(),
            ]
        );
    }
}

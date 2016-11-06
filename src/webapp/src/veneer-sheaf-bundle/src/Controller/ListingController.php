<?php

namespace Veneer\SheafBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Controller\WorkspaceRepoController;
use Symfony\Component\Yaml\Yaml;
use Veneer\OpsBundle\Service\Editor\DeploymentFormHelper;
use Veneer\BoshBundle\Controller\DeploymentController;
use Veneer\BoshBundle\Entity\Deployments;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;

class ListingController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, $path)
    {
        return CloudConfigController::defNav($nav)
            ->add(
                'editor',
                [
                    'veneer_ops_workspace_app_cloudconfig_summary' => [
                        'path' => $path,
                    ],
                ],
                [
                    'fontawesome' => 'pencil',
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

        $logo = base64_encode(file_get_contents($sheafPath . '/logo.png'));
        $spec = Yaml::parse(file_get_contents($sheafPath . '/spec.yml'));

        foreach ($spec['components'] as $componentIndex => $component) {
            $componentSpec = Yaml::parse(file_get_contents($sheafPath . '/' . $component['name'] . '/spec.yml'));
            $componentSpec['name'] = $component['name'];
            $spec['components'][$componentIndex] = $componentSpec;
        }

        return $this->renderApi(
            'VeneerSheafBundle:Listing:summary.html.twig',
            [
                'sheaf' => $sheaf,
                'logo' => $logo,
                'spec' => $spec,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), 'asdf'),
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
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), 'asdf'),
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
                'readme' => file_exists($sheafPath . '/README.md') ? file_get_contents($sheafPath . '/README.md') : null,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), 'asdf'),
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

        $logo = base64_encode(file_get_contents($sheafPath . '/logo.png'));
        $spec = Yaml::parse(file_get_contents($sheafPath . '/spec.yml'));

        $bulkFormBuilder = $this->container->get('form.factory')->createNamedBuilder('data');
        $bulkFormBuilder->setData([
            'name' => $spec['name'],
        ]);
        $bulkFormBuilder->add(
            'name',
            'text',
            [
                'label' => 'Name',
                'veneer_help_html' => 'This name will become a prefix for all installed components.'
            ]
        );

        foreach ($spec['components'] as $componentIndex => $component) {
            $componentSpec = Yaml::parse(file_get_contents($sheafPath . '/' . $component['name'] . '/spec.yml'));
            $componentSpec['name'] = $component['name'];
            $spec['components'][$componentIndex] = $componentSpec;

            $componentForm = $bulkFormBuilder->add($component['name'], 'form')->get($component['name']);

            foreach ($componentSpec['features'] as $feature) {
                $choices = [];

                foreach ($feature['choices'] as $choice) {
                    $choices[$choice['name']] = $choice['title'];
                }

                $componentForm->add(
                    $feature['name'],
                    'choice',
                    [
                        'choices' => $choices,
                        'expanded' => true,
                        'required' => isset($feature['required']) ? $feature['required'] : true,
                        'multiple' => isset($feature['multiple']) ? $feature['multiple'] : false,
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
                    'veneer_sheaf_workspace_app_sheaf_summary',
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
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), 'asdf'),
                'form' => $bulkForm->createView(),
            ]
        );
    }
}

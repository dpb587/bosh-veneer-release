<?php

namespace Veneer\OpsBundle\Controller;

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

class WorkspaceAppSheafController extends AbstractController
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
        $path = $request->query->get('path', 'test.yml');
        $sheaf = __DIR__ . '/../../../../../../valise-test/sheaf/concourse/0.0.1';

        $logo = base64_encode(file_get_contents($sheaf . '/logo.png'));
        $spec = Yaml::parse(file_get_contents($sheaf . '/spec.yml'));

        $bulkFormBuilder = $this->container->get('form.factory')->createNamedBuilder('data');
        $bulkFormBuilder->add(
            'name',
            'text',
            [
                'label' => 'Name',
                'veneer_help_html' => 'This name will become a prefix for all installed components.'
            ]
        );

        foreach ($spec['components'] as $componentIndex => $component) {
            $componentSpec = Yaml::parse(file_get_contents($sheaf . '/' . $component['name'] . '/spec.yml'));
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

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppSheaf:summary.html.twig',
            [
                'logo' => $logo,
                'spec' => $spec,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path),
                'form' => $bulkForm->createView(),
            ]
        );
    }

    public function createAction(Request $request, $section)
    {
        die(print_r($request->request->all(), true));
    }
}

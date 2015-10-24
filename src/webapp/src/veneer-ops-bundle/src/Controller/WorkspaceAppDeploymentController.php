<?php

namespace Veneer\OpsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Controller\WorkspaceRepoController;
use Symfony\Component\Yaml\Yaml;
use Veneer\OpsBundle\Service\DeploymentFormHelper;
use Veneer\BoshBundle\Controller\DeploymentController;
use Veneer\BoshBundle\Entity\Deployments;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;

class WorkspaceAppDeploymentController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, $path, $name)
    {
        $mock = new Deployments();
        $refl = new \ReflectionProperty($mock, 'name');
        $refl->setAccessible(true);
        $refl->setValue($mock, $name);

        return DeploymentController::defNav($nav, [ 'deployment' => $mock ])
            ->add(
                'editor',
                [
                    'veneer_ops_workspace_app_deployment_summary' => [
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
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($path));

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppDeployment:summary.html.twig',
            [
                'path' => $path,
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path, $yaml['name']),
                'sidenav_active' => 'summary',
            ]
        );
    }

    public function sectionAction(Request $request, $section)
    {
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($path));

        $navSection = $section;
        $tplExtras = [];

        if ('properties' == $section) {
            $deploymentPropertySpecHelper = $this->container->get('veneer_bosh.deployment_property_spec_helper');

            if ($request->query->has('job')) {
                $filterJob = $request->query->get('job');
                $foundJob = false;

                foreach ($yaml['jobs'] as $job) {
                    if ($job['name'] != $filterJob) {
                        continue;
                    }

                    $foundJob = true;

                    break;
                }

                if (!$foundJob) {
                    throw new NotFoundHttpException('Failed to find job');
                }

                $propertyTemplates = DeploymentPropertySpecHelper::collectReleaseTemplates($yaml, $filterJob);
                $tplExtras['properties_configured'] = isset($job['properties']) ? $job['properties'] : null;
                $tplExtras['properties_editpath'] = 'jobs[' . $filterJob . '].properties.';
                $navSection = 'jobs';
            } else {
                $propertyTemplates = DeploymentPropertySpecHelper::collectReleaseTemplates($yaml);
                $tplExtras['properties_configured'] = isset($yaml['properties']) ? $yaml['properties'] : null;
                $tplExtras['properties_editpath'] = 'properties.';
            }

            $merged = $deploymentPropertySpecHelper->mergeTemplatePropertiesSpecs($propertyTemplates);
            $propertyTree = $deploymentPropertySpecHelper->convertSpecToTree($merged);

            $tplExtras['properties_tree'] = $propertyTree;
        }

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppDeployment:section-' . $section . '.html.twig',
            array_merge(
                [
                    'path' => $path,
                    'manifest' => $yaml,
                ],
                $tplExtras
            ),
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path, $yaml['name'])
                    ->add(
                        $navSection,
                        [
                            'veneer_ops_workspace_app_deployment_section' => [
                                'section' => $navSection,
                                'path' => $path,
                            ],
                        ]
                    ),
                'sidenav_active' => $navSection,
            ]
        );
    }

    public function editAction(Request $request)
    {
        $path = $request->query->get('path');
        $property = $request->query->get('property');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $yaml = Yaml::parse($repo->showFile($path));

        $editor = new DeploymentFormHelper($this->container->get('form.factory'), $this->container->get('veneer_bosh.deployment_property_spec_helper'));
        $editorProfile = $editor->lookup($yaml, $property);

        if ($request->request->has($editorProfile['form']->getName())) {
            $editorProfile['form']->bind($request);

            if ($editorProfile['form']->isValid()) {
                $accessor = PropertyAccess::createPropertyAccessor();

                $accessor->setValue($yaml, $editorProfile['path'], $editorProfile['form']->getData());

                die(print_r($yaml, true));
            }
        }

        $section = str_replace('_', '', preg_replace('/^([^\.\[]+)(.*)$/', '$1', $property));
        $nav = self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path, $yaml['name']);

        if (in_array($section, [ 'compilation', 'update' ])) {
            $nav->add($section);
        } else {
            $nav->add(
                $section,
                [
                    'veneer_ops_workspace_app_deployment_section' => [
                        'section' => $section,
                        'path' => $path,
                    ],
                ]
            );
        }

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppDeployment:edit.html.twig',
            [
                'path' => $path,
                'manifest' => $yaml,
                'property' => $property,
                'title' => $editorProfile['title'],
                'form' => $editorProfile['form']->createView(),
            ],
            [
                'def_nav' => $nav,
                'sidenav_active' => $section,
            ]
        );
    }
}

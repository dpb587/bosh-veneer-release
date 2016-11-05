<?php

namespace Veneer\OpsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Controller\WorkspaceRepoController;
use Symfony\Component\Yaml\Yaml;
use Veneer\OpsBundle\Service\Editor\DeploymentFormHelper;
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
        $draftProfile = $repo->getDraftProfile('ops-deployment-' . substr(md5($path), 0, 8), $path);

        $yaml = $this->loadData($repo, $path, $draftProfile);

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppDeployment:summary.html.twig',
            [
                'draft_profile' => $draftProfile,
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
        $draftProfile = $repo->getDraftProfile('ops-deployment-' . substr(md5($path), 0, 8), $path);

        $yaml = $this->loadData($repo, $path, $draftProfile);

        $navSection = $section;
        $tplExtras = [];

        if ('properties' == $section) {
            $deploymentPropertySpecHelper = $this->container->get('veneer_bosh.deployment_property_spec_helper');

            if ($request->query->has('instance_group')) {
                $filterJob = $request->query->get('instance_group');
                $foundJob = false;

                foreach ($yaml['instance_groups'] as $job) {
                    if ($job['name'] != $filterJob) {
                        continue;
                    }

                    $foundJob = true;

                    break;
                }

                if (!$foundJob) {
                    throw new NotFoundHttpException('Failed to find instance group');
                }

                $propertyTemplates = DeploymentPropertySpecHelper::collectReleaseJobs($yaml, $filterJob);
                $tplExtras['properties_configured'] = isset($job['properties']) ? $job['properties'] : null;
                $tplExtras['properties_editpath'] = 'instance_groups[' . $filterJob . '].properties.';
                $navSection = 'instance-groups';
            } else {
                $propertyTemplates = DeploymentPropertySpecHelper::collectReleaseJobs($yaml);
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
                    'draft_profile' => $draftProfile,
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
        $raw = $request->query->get('raw');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $draftProfile = $repo->getDraftProfile('ops-deployment-' . substr(md5($path), 0, 8), $path);

        $yaml = $this->loadData($repo, $path, $draftProfile);

        $editor = new DeploymentFormHelper($this->container->get('form.factory'), $this->container->get('veneer_bosh.deployment_property_spec_helper'));
        $editorProfile = $editor->lookup($yaml, $path, $property, filter_var($raw, FILTER_VALIDATE_BOOLEAN));

        $section = str_replace('_', '-', preg_replace('/^([^\.\[]+)(.*)$/', '$1', $property));
        $nav = self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path, $yaml['name']);

        if (($property === null) || in_array($section, [ 'compilation', 'update' ])) {
            $nav->add(
                $editorProfile['title'],
                [
                    'veneer_ops_workspace_app_deployment_edit' => [
                        'section' => $section,
                        'path' => $path,
                        'property' => $property,
                        'raw' => $raw,
                    ],
                ]
            );
        } else {
            $nav
                ->add(
                    $section,
                    [
                        'veneer_ops_workspace_app_deployment_section' => [
                            'section' => $section,
                            'path' => $path,
                        ],
                    ]
                )
                ->add(
                    $section,
                    [
                        'veneer_ops_workspace_app_deployment_edit' => [
                            'section' => $section,
                            'path' => $path,
                            'property' => $property,
                            'raw' => $raw,
                        ],
                    ]
                )
                ;
        }

        if ($request->request->has($editorProfile['form']->getName())) {
            $editorProfile['form']->bind($request);

            if ($editorProfile['form']->isValid()) {
                $data = $editorProfile['form']->getData();

                if ($property !== null) {
                    $accessor = PropertyAccess::createPropertyAccessor();

                    $accessor->setValue($yaml, $editorProfile['path'], $data);

                    $data = Yaml::dump($yaml, 8);
                }

                $repo->commitWrites(
                    $draftProfile,
                    [
                        $path => $data,
                    ],
                    'Update ' . $request->query->get('property')
                );

                return $this->redirect($nav[-2]['url']);
            }
        }

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppDeployment:edit.html.twig',
            [
                'draft_profile' => $draftProfile,
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

    protected function loadData(RepositoryInterface $repo, $path, array $draftProfile)
    {
        if ($repo->fileExists($path, $draftProfile['ref_read'])) {
            return Yaml::parse($repo->showFile($path, $draftProfile['ref_read'])) ?: [];
        }

        return null;
    }
}

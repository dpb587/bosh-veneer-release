<?php

namespace Veneer\BoshEditorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\BoshBundle\Controller\DeploymentALLController;
use Veneer\BoshEditorBundle\Service\Editor\SchemaMapFormHelper;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\BoshEditorBundle\Service\Editor\DeploymentFormHelper;
use Veneer\BoshBundle\Controller\DeploymentController;
use Veneer\BoshBundle\Entity\Deployments;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Veneer\BoshBundle\Service\DeploymentPropertySpecHelper;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\CoreBundle\Plugin\RequestContext\Annotations as CoreContext;

/**
 * @CoreContext\AppPath(name = "ops-deployment")
 */
class AppDeploymentController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return DeploymentALLController::defNav($nav)
            ->add(
                'editor',
                [
                    'veneer_bosh_editor_app_deployment_summary' => [
                        'file' => $_bosh['app']['file'],
                    ],
                ],
                [
                    'fontawesome' => 'pencil',
                ]
            )
        ;
    }

    public function summaryAction(Request $request, Context $_bosh)
    {
        $yaml = $this->loadData($_bosh['app']['file'], $_bosh['app']['profile']);

        return $this->renderApi(
            'VeneerBoshEditorBundle:AppDeployment:summary.html.twig',
            [
                'draft_profile' => $_bosh['app']['profile'],
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
                'sidenav_active' => 'summary',
            ]
        );
    }

    public function sectionAction(Request $request, Context $_bosh, $section)
    {
        $yaml = $this->loadData($_bosh['app']['file'], $_bosh['app']['profile']);

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
                $tplExtras['properties_editpath'] = 'instance_groups['.$filterJob.'].properties.';
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
            'VeneerBoshEditorBundle:AppDeployment:section-'.$section.'.html.twig',
            array_merge(
                [
                    'draft_profile' => $_bosh['app']['profile'],
                    'manifest' => $yaml,
                ],
                $tplExtras
            ),
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh)
                    ->add(
                        $navSection,
                        [
                            'veneer_bosh_editor_app_deployment_section' => [
                                'section' => $navSection,
                                'file' => $_bosh['app']['file'],
                            ],
                        ]
                    ),
                'sidenav_active' => $navSection,
            ]
        );
    }

    public function editAction(Request $request, Context $_bosh)
    {
        $path = $request->query->get('path');

        $editor = new SchemaMapFormHelper(
            $this->container->get('form.factory'),
            $this->container->get('veneer_core.schema_map.form_builder'),
            $this->container->get('veneer_bosh.schema_map.deployment_v2')
        );

        $editorNode = $editor->getEditorNode($this->loadData($_bosh['app']['file'], $_bosh['app']['profile']), $path);
        $editorProfile = $editor->createEditor($editorNode);

        $section = str_replace('_', '-', explode('/', $editorNode->getData()->getPath())[1]);

        $nav = self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh);
        $nav->add(
            'edit',
            [
                'veneer_bosh_editor_app_deployment_edit' => [
                    'file' => $_bosh['app']['file'],
                    'path' => $path,
                ],
            ]
        );

        if ($request->request->has($editorProfile['form']->getName())) {
            $editorProfile['form']->bind($request);

            if ($editorProfile['form']->isValid()) {
                $data = $editorProfile['form']->getData();

                if ($path !== null) {
                    $editorNode->getData()->setData($data);

                    $data = Yaml::dump($editorNode->getData()->getRoot()->getData(), 8);
                }

                $this->container->get('veneer_core.workspace.repository')->commitWrites(
                    $_bosh['app']['profile'],
                    [
                        $_bosh['app']['file'] => $data,
                    ],
                    sprintf('Update cloud-config (%s)', $path)
                );

                return $this->redirect($nav[-2]['url']);
            }
        }

        return $this->renderApi(
            'VeneerBoshEditorBundle:AppDeployment:edit.html.twig',
            [
                'draft_profile' => $_bosh['app']['profile'],
                'section' => $section,
                'path' => $path,
                'title' => $editorProfile['title'],
                'form' => $editorProfile['form']->createView(),
            ],
            [
                'def_nav' => $nav,
                'sidenav_active' => $section,
            ]
        );
    }

    protected function loadData($path, array $draftProfile)
    {
        $repo = $this->container->get('veneer_core.workspace.repository');

        if ($repo->fileExists($path, $draftProfile['ref_read'])) {
            return Yaml::parse($repo->showFile($path, $draftProfile['ref_read'])) ?: [];
        }

        return [];
    }
}

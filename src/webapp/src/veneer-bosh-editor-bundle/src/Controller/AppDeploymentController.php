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
use Veneer\CoreBundle\Service\SchemaMap\DataNode\ArrayDataNode;
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
                        'file' => $_bosh['app']['file']->getPath(),
                    ],
                ],
                [
                    'fontawesome' => 'pencil',
                ]
            )
        ;
    }

    public function summaryAction(Context $_bosh)
    {
        $manifest = Yaml::parse($_bosh['app']['file']->getData()) ?: [];

        return $this->renderApi(
            'VeneerBoshEditorBundle:AppDeployment:summary.html.twig',
            [
                'draft_profile' => $_bosh['app']['profile'],
                'manifest' => $manifest,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh),
                'sidenav_active' => 'summary',
            ]
        );
    }

    public function sectionAction(Request $request, Context $_bosh, $section)
    {
        $manifest = Yaml::parse($_bosh['app']['file']->getData()) ?: [];

        $navSection = $section;
        $tplExtras = [];

        if ('properties' == $section) {
            $path = $request->query->get('path', '/properties');

            if (preg_match('#^/properties(/.+|)$#', $path)) {
                $tplExtras['title'] = 'Deployment Properties';
            } elseif (preg_match('#^/instance_groups/([^/]+)/properties(/.+|)$#', $path)) {
                $tplExtras['title'] = 'Instance Group Properties';
            } elseif (preg_match('#^/instance_groups/([^/]+)/jobs/([^/]+)/properties(/.+|)$#', $path)) {
                $tplExtras['title'] = 'Job Properties';
            } else {
                throw new NotFoundHttpException('Matching properties path to expected scopes');
            }

            $schemaTuple = $this->container->get('veneer_bosh.schema_map.deployment_v2')
                ->traverse((new ArrayDataNode(''))->setData($manifest), $path);

            $dataNode = $schemaTuple->getData();
            $schemaNode = $schemaTuple->getSchema();

            // @todo should probably fully resolve schema (in case sub-types are referenced)

            $tplExtras['properties_schema'] = json_decode(json_encode($schemaNode->getSchema()), true);
            $tplExtras['properties_values'] = $dataNode->getData();
            $tplExtras['properties_path'] = $path;
        } elseif ('instance-group' == $section) {
            $path = $request->query->get('path');

            if (preg_match('#^/instance_groups(/.+|)$#', $path)) {
                $tplExtras['title'] = 'Instance Group';
            } else {
                throw new NotFoundHttpException('Matching instance_groups path to expected scopes');
            }

            $schemaTuple = $this->container->get('veneer_bosh.schema_map.deployment_v2')
                ->traverse((new ArrayDataNode(''))->setData($manifest), $path);

            $dataNode = $schemaTuple->getData();

            $tplExtras['path'] = $path;
            $tplExtras['instance_group'] = $dataNode->getData();
        }

        return $this->renderApi(
            'VeneerBoshEditorBundle:AppDeployment:section-'.$section.'.html.twig',
            array_merge(
                [
                    'draft_profile' => $_bosh['app']['profile'],
                    'manifest' => $manifest,
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

        $manifest = Yaml::parse($_bosh['app']['file']->getData()) ?: [];

        $editorNode = $editor->getEditorNode($manifest, $path);
        $editorProfile = $editor->createEditor($editorNode);

        $section = str_replace('_', '-', explode('/', $editorNode->getData()->getPath())[1]);

        $nav = self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh);
        $nav->add(
            'edit',
            [
                'veneer_bosh_editor_app_deployment_edit' => [
                    'file' => $_bosh['app']['file']->getPath(),
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

//                $this->container->get('veneer_core.workspace.repository')->commitWrites(
//                    $_bosh['app']['profile'],
//                    [
//                        $_bosh['app']['file'] => $data,
//                    ],
//                    sprintf('Update cloud-config (%s)', $path)
//                );

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
                'manifest' => $manifest,
            ]
        );
    }
}

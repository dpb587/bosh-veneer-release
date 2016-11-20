<?php

namespace Veneer\BoshEditorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\BoshEditorBundle\Service\Editor\SchemaMapFormHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Veneer\CoreBundle\Plugin\RequestContext\Annotations as CoreContext;

/**
 * @CoreContext\AppPath(name = "ops-deployment")
 */
class AppCloudConfigController extends AbstractController
{
    public function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return CloudConfigController::defNav($nav)
            ->add(
                'editor',
                [
                    'veneer_bosh_editor_app_cloudconfig_summary' => [
                        'file' => $_bosh['app']['file'],
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
        $yaml = $this->loadData($_bosh['app']['file'], $_bosh['app']['profile']);

        return $this->renderApi(
            'VeneerBoshEditorBundle:AppCloudConfig:summary.html.twig',
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

    public function sectionAction(Context $_bosh, $section)
    {
        $yaml = $this->loadData($_bosh['app']['file'], $_bosh['app']['profile']);

        $navSection = $section;

        return $this->renderApi(
            'VeneerBoshEditorBundle:AppCloudConfig:section-'.$section.'.html.twig',
            [
                'draft_profile' => $_bosh['app']['profile'],
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh)
                    ->add(
                        $navSection,
                        [
                            'veneer_bosh_editor_app_cloudconfig_section' => [
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
            $this->container->get('veneer_bosh.schema_map.cloud_config')
        );

        $editorNode = $editor->getEditorNode($this->loadData($_bosh['app']['file'], $_bosh['app']['profile']), $path);
        $editorProfile = $editor->createEditor($editorNode);

        $section = str_replace('_', '-', explode('/', $editorNode->getData()->getPath())[1]);

        $nav = self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $_bosh);
        $nav->add(
            'edit',
            [
                'veneer_bosh_editor_app_cloudconfig_edit' => [
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
            'VeneerBoshEditorBundle:AppCloudConfig:edit.html.twig',
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

    protected function loadData($file, array $draftProfile)
    {
        $repo = $this->container->get('veneer_core.workspace.repository');

        if ($repo->fileExists($file, $draftProfile['ref_read'])) {
            return Yaml::parse($repo->showFile($file, $draftProfile['ref_read'])) ?: [];
        }

        return null;
    }
}

<?php

namespace Veneer\OpsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\OpsBundle\Service\Editor\CloudConfigFormHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class WorkspaceAppCloudConfigController extends AbstractController
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
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $draftProfile = $repo->getDraftProfile('ops-deployment-'.substr(md5($path), 0, 8), $path);

        $yaml = $this->loadData($repo, $path, $draftProfile);

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppCloudConfig:summary.html.twig',
            [
                'draft_profile' => $draftProfile,
                'path' => $path,
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path),
                'sidenav_active' => 'summary',
            ]
        );
    }

    public function sectionAction(Request $request, $section)
    {
        $path = $request->query->get('path');
        $repo = $this->container->get('veneer_core.workspace.repository');
        $draftProfile = $repo->getDraftProfile('ops-deployment-'.substr(md5($path), 0, 8), $path);

        $yaml = $this->loadData($repo, $path, $draftProfile);

        $navSection = $section;

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppCloudConfig:section-'.$section.'.html.twig',
            [
                'draft_profile' => $draftProfile,
                'path' => $path,
                'manifest' => $yaml,
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path)
                    ->add(
                        $navSection,
                        [
                            'veneer_ops_workspace_app_cloudconfig_section' => [
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
        $draftProfile = $repo->getDraftProfile('ops-deployment-'.substr(md5($path), 0, 8), $path);

        $yaml = $this->loadData($repo, $path, $draftProfile);

        $editor = new CloudConfigFormHelper($this->container->get('form.factory'));
        $editorProfile = $editor->lookup($yaml, $path, $property, filter_var($raw, FILTER_VALIDATE_BOOLEAN));

        $section = str_replace('_', '-', preg_replace('/^([^\.\[]+)(.*)$/', '$1', $property));
        $nav = self::defNav($this->container->get('veneer_bosh.breadcrumbs'), $path);

        if (($property === null) || in_array($section, ['compilation', 'update'])) {
            $nav->add(
                $editorProfile['title'],
                [
                    'veneer_ops_workspace_app_cloudconfig_edit' => [
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
                        'veneer_ops_workspace_app_cloudconfig_section' => [
                            'section' => $section,
                            'path' => $path,
                        ],
                    ]
                )
                ->add(
                    $section,
                    [
                        'veneer_ops_workspace_app_cloudconfig_edit' => [
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
                    'Update cloud config'.(isset($property) ? (' ('.$property.')') : '')
                );

                return $this->redirect($nav[-2]['url']);
            }
        }

        return $this->renderApi(
            'VeneerOpsBundle:WorkspaceAppCloudConfig:edit.html.twig',
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

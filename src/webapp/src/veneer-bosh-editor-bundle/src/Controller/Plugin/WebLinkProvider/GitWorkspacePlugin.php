<?php

namespace Veneer\BoshEditorBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class GitWorkspacePlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_runtimeconfig_summary':
                return [
                    (new Link('editor'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Runtime Config')
                        ->setRoute(
                            'veneer_bosh_editor_app_runtimeconfig_summary',
                            [
                                'path' => 'bosh/runtime-config/manifest.yml',
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_summary':
                return [
                    (new Link('editor'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Instance Group')
                        ->setRoute(
                            'veneer_bosh_editor_app_deployment_edit',
                            [
                                'path' => 'bosh/deployment/'.$_bosh['deployment']['name'].'/manifest.yml',
                                'property' => 'instance_groups['.$_bosh['instance_group']['job'].']',
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_summary':
                return [
                    (new Link('editor'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Deployment')
                        ->setRoute(
                            'veneer_core_workspace_repo_app',
                            [
                                'path' => 'bosh/deployment/'.$_bosh['deployment']['name'].'/manifest.yml',
                            ]
                        ),
                ];
        }

        return [];
    }
}

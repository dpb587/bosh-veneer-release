<?php

namespace Veneer\OpsBundle\Controller\Plugin\WebLinkProvider;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class LiveWorkspacePlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_cloudconfig_summary':
                return [
                    (new Link('ops_edit'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Cloud Config')
                        ->setRoute(
                            'veneer_ops_workspace_app_cloudconfig_summary',
                            [
                                'path' => 'cloud-config.yml',
                            ]
                        ),
                ];
            case 'veneer_bosh_runtimeconfig_summary':
                return [
                    (new Link('ops_edit'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Runtime Config')
                        ->setRoute(
                            'veneer_ops_workspace_app_runtimeconfig_summary',
                            [
                                'path' => 'runtime-config.yml',
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_summary':
                return [
                    (new Link('ops_edit'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Instance Group')
                        ->setRoute(
                            'veneer_ops_workspace_app_deployment_edit',
                            [
                                'path' => 'deployments/' . $_bosh['deployment']['name'] . '.yml',
                                'property' => 'instance_groups[' . $_bosh['instance_group']['job'] . ']',
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_summary':
                return [
                    (new Link('ops_edit'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Edit Deployment')
                        ->setRoute(
                            'veneer_core_workspace_repo_app',
                            [
                                'path' => 'deployments/' . $_bosh['deployment']['name'] . '.yml',
                            ]
                        ),
                ];
        }

        return [];
    }
}

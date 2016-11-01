<?php

namespace Veneer\BoshBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class WidgetPlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deployment_summary':
                return [
                    (new Link('manifest'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_manifest',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                            ]
                        ),
                    (new Link('releases'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_releases',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                            ]
                        ),
                    (new Link('stemcells'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_stemcells',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                            ]
                        ),
                    (new Link('jobALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_jobALL_index',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                            ]
                        ),
                    (new Link('vmALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_vmALL_index',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_summary':
                return [
                    (new Link('indexALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_idALL_index',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                            ]
                        ),
                    (new Link('restart'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Restart')
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_restart',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                            ]
                        ),
                    (new Link('recreate'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Recreate')
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_recreate',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_id_summary':
                return [
                    (new Link('vm'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_id_vm',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                            ]
                        ),
                    (new Link('persistentdiskALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_id_persistentdiskALL_index',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                            ]
                        ),
                    (new Link('restart'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Restart')
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_id_restart',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                            ]
                        ),
                    (new Link('recreate'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Recreate')
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_id_recreate',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_id_persistentdisk_summary':
                return [
                    (new Link('cpi'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_id_persistentdisk_cpi',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                                'persistent_disk' => $_bosh['persistent_disk']['id'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_vm_summary':
                return [
                    (new Link('applyspec'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_vm_applyspec',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'agent' => $_bosh['vm']['agentId'],
                            ]
                        ),
                    (new Link('instance'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_vm_instance',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'agent' => $_bosh['vm']['agentId'],
                            ]
                        ),
                    (new Link('packages'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_vm_packages',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'agent' => $_bosh['vm']['agentId'],
                            ]
                        ),
                    (new Link('templates'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_vm_templates',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'agent' => $_bosh['vm']['agentId'],
                            ]
                        ),
                    (new Link('networkALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_vm_networkALL_index',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'agent' => $_bosh['vm']['agentId'],
                            ]
                        ),
                    (new Link('resourcepool_cpi'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_vm_resourcepool_cpi',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'agent' => $_bosh['vm']['agentId'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_vm_network_summary':
                return [
                    (new Link('cpi'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_vm_network_cpi',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'agent' => $_bosh['vm']['agentId'],
                                'network' => $_bosh['network']['name'],
                            ]
                        ),
                ];
            case 'veneer_bosh_task_summary':
                return [
                    (new Link('events'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_task_events',
                            [
                                'task' => $_bosh['task']['id'],
                            ]
                        ),
                ];
            case 'veneer_bosh_release_summary':
                return [
                    (new Link('packageALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_release_packageALL_index',
                            [
                                'release' => $_bosh['release']['name'],
                            ]
                        ),
                    (new Link('versionALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_release_versionALL_index',
                            [
                                'release' => $_bosh['release']['name'],
                            ]
                        ),
                ];
            case 'veneer_bosh_release_template_summary':
                return [
                    (new Link('properties'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_release_template_properties',
                            [
                                'release' => $_bosh['release']['name'],
                                'template' => $_bosh['template']['name'],
                                'version' => $_bosh['template']['version'],
                            ]
                        ),
                ];
            case 'veneer_bosh_release_version_summary':
                return [
                    (new Link('deployments'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_release_version_deployments',
                            [
                                'release' => $_bosh['release']['name'],
                                'version' => $_bosh['version']['version'],
                            ]
                        ),
                    (new Link('packages'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_release_version_packages',
                            [
                                'release' => $_bosh['release']['name'],
                                'version' => $_bosh['version']['version'],
                            ]
                        ),
                    (new Link('templates'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_release_version_templates',
                            [
                                'release' => $_bosh['release']['name'],
                                'version' => $_bosh['version']['version'],
                            ]
                        ),
                    (new Link('properties'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_release_version_properties',
                            [
                                'release' => $_bosh['release']['name'],
                                'version' => $_bosh['version']['version'],
                            ]
                        ),
                ];
            default:
                return [];
        }

        return [];
    }
}

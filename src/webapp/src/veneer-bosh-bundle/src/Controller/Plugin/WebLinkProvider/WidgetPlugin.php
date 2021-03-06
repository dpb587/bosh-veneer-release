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
            case 'veneer_bosh_cloudconfig_summary':
                return [
                    (new Link('manifest'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute('veneer_bosh_cloudconfig_manifest'),
                ];
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
                ];
            case 'veneer_bosh_deployment_instancegroup_summary':
                return [
                    (new Link('indexALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instanceALL_index',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                            ]
                        ),
                    (new Link('restart'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Restart')
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_restart',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                            ]
                        ),
                    (new Link('recreate'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Recreate')
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_recreate',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_summary':
                return [
                    (new Link('networkALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_networkALL_index',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ]
                        ),
                    (new Link('persistentdiskALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_persistentdiskALL_index',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ]
                        ),
                    (new Link('restart'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Restart')
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_restart',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ]
                        ),
                    (new Link('recreate'))
                        ->setTopic(Link::TOPIC_CONFIG)
                        ->setTitle('Recreate')
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_recreate',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ]
                        ),
                    (new Link('spec'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_spec',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ]
                        ),
                    (new Link('packages'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_packages',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ]
                        ),
                    (new Link('templates'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_templates',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_persistentdisk_summary':
                return [
                    (new Link('cpi'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_persistentdisk_cpi',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
                                'persistent_disk' => $_bosh['persistent_disk']['id'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_network_summary':
                return [
                    (new Link('cpi'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_deployment_instancegroup_instance_network_cpi',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'instance_group' => $_bosh['instance_group']['job'],
                                'instance' => $_bosh['instance']['uuid'],
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
            case 'veneer_bosh_release_job_summary':
                return [
                    (new Link('properties'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_release_job_properties',
                            [
                                'release' => $_bosh['release']['name'],
                                'job' => $_bosh['job']['name'],
                                'version' => $_bosh['job']['version'],
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
            case 'veneer_bosh_stemcell_summary':
                return [
                    (new Link('versionALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_stemcell_versionALL_index',
                            [
                                'stemcell' => $_bosh['stemcell']['name'],
                            ]
                        ),
                ];
            case 'veneer_bosh_stemcell_version_summary':
                return [
                    (new Link('deployments'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_bosh_stemcell_version_deployments',
                            [
                                'stemcell' => $_bosh['stemcell']['name'],
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

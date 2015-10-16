<?php

namespace Veneer\LogsearchBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\WebBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\WebBundle\Plugin\LinkProvider\Link;

class WidgetPlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deployment_summary':
                return [
                    (new Link('logsearch_monitstatus'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_monitstatus',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instance_summary':
                return [
                    (new Link('diskstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_instance_diskstats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job_name' => $_bosh['instance']['job'],
                                'job_index' => $_bosh['instance']['index'],
                            ]
                        ),
                    (new Link('loadstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_instance_loadstats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job_name' => $_bosh['instance']['job'],
                                'job_index' => $_bosh['instance']['index'],
                            ]
                        ),
                    (new Link('memstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_instance_memstats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job_name' => $_bosh['instance']['job'],
                                'job_index' => $_bosh['instance']['index'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_instance_persistentdisk_summary':
                return [
                    (new Link('hoststats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_instance_persistentdisk_hoststats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job_name' => $_bosh['instance']['job'],
                                'job_index' => $_bosh['instance']['index'],
                                'persistent_disk' => $_bosh['persistent_disk']['id'],
                            ]
                        ),
                ];
            case 'veneer_bosh_deployment_vm_network_summary':
                return [
                    (new Link('hoststats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_logsearch_deployment_vm_network_hoststats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'agent' => $_bosh['vm']['agentId'],
                                'network' => $_bosh['network']['name'],
                            ]
                        ),
                ];
            default:
                return [];
        }
    }
}

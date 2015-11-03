<?php

namespace Veneer\AwsCpiBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class WidgetPlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deployment_job_index_persistentdisk_summary':
                return [
                    (new Link('cloudwatchbytesstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_awscpi_deployment_job_index_persistentdisk_cloudwatchbytesstats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                                'persistent_disk' => $_bosh['persistent_disk']['id'],
                            ]
                        ),
                    (new Link('cloudwatchopsstats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_awscpi_deployment_job_index_persistentdisk_cloudwatchopsstats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                                'persistent_disk' => $_bosh['persistent_disk']['id'],
                            ]
                        ),
                    (new Link('cloudwatchqueuestats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_awscpi_deployment_job_index_persistentdisk_cloudwatchqueuestats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                                'persistent_disk' => $_bosh['persistent_disk']['id'],
                            ]
                        ),
                    (new Link('cloudwatchidlestats'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_awscpi_deployment_job_index_persistentdisk_cloudwatchidlestats',
                            [
                                'deployment' => $_bosh['deployment']['name'],
                                'job' => $_bosh['job']['job'],
                                'index' => $_bosh['index']['index'],
                                'persistent_disk' => $_bosh['persistent_disk']['id'],
                            ]
                        ),
                ];
            default:
                return [];
        }
    }
}

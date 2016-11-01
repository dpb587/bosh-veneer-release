<?php

namespace Veneer\HubBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class WidgetPlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_hub_hub_release_summary':
                return [
                    (new Link('versionALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_hub_hub_release_versionALL_index',
                            [
                                'hub' => $_bosh['hub']['name'],
                                'release' => $_bosh['release']['name'],
                            ]
                        ),
                ];
            case 'veneer_hub_hub_release_version_summary':
                return [
                    (new Link('upload'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_hub_hub_release_version_upload',
                            [
                                'hub' => $_bosh['hub']['name'],
                                'release' => $_bosh['release']['name'],
                                'version' => $_bosh['version']->getVersion(),
                            ]
                        ),
                ];
            case 'veneer_hub_hub_stemcell_summary':
                return [
                    (new Link('versionALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_hub_hub_stemcell_versionALL_index',
                            [
                                'hub' => $_bosh['hub']['name'],
                                'stemcell' => $_bosh['stemcell']['name'],
                            ]
                        ),
                ];
            case 'veneer_hub_hub_stemcell_version_summary':
                return [
                    (new Link('upload'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_hub_hub_stemcell_version_upload',
                            [
                                'hub' => $_bosh['hub']['name'],
                                'stemcell' => $_bosh['stemcell']['name'],
                                'version' => $_bosh['version']->getVersion(),
                            ]
                        ),
                ];
            default:
                return [];
        }

        return [];
    }
}

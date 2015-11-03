<?php

namespace Veneer\MarketplaceBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class WidgetPlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_marketplace_marketplace_release_summary':
                return [
                    (new Link('versionALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_marketplace_marketplace_release_versionALL_index',
                            [
                                'marketplace' => $_bosh['marketplace']['name'],
                                'release' => $_bosh['release']['name'],
                            ]
                        ),
                ];
            case 'veneer_marketplace_marketplace_release_version_summary':
                return [
                    (new Link('upload'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_marketplace_marketplace_release_version_upload',
                            [
                                'marketplace' => $_bosh['marketplace']['name'],
                                'release' => $_bosh['release']['name'],
                                'version' => $_bosh['version']->getVersion(),
                            ]
                        ),
                ];
            case 'veneer_marketplace_marketplace_stemcell_summary':
                return [
                    (new Link('versionALL'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_marketplace_marketplace_stemcell_versionALL_index',
                            [
                                'marketplace' => $_bosh['marketplace']['name'],
                                'stemcell' => $_bosh['stemcell']['name'],
                            ]
                        ),
                ];
            case 'veneer_marketplace_marketplace_stemcell_version_summary':
                return [
                    (new Link('upload'))
                        ->setTopic(Link::TOPIC_WIDGET)
                        ->setRoute(
                            'veneer_marketplace_marketplace_stemcell_version_upload',
                            [
                                'marketplace' => $_bosh['marketplace']['name'],
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

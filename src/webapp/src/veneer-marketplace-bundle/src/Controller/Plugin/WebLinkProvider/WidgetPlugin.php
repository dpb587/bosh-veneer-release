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
            default:
                return [];
        }

        return [];
    }
}

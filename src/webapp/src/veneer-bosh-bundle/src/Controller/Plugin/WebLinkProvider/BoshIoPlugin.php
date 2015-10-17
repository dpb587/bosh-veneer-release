<?php

namespace Veneer\BoshBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class BoshIoPlugin implements PluginInterface
{
    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deploymentALL_index':
                return [
                    (new Link('boshio_deploymentmanifestdoc'))
                        ->setTopic(Link::TOPIC_DOCUMENTATION)
                        ->setTitle('bosh.io')
                        ->setNote('creating manifests')
                        ->setUrl('https://bosh.io/docs/deployment-manifest.html'),
                ];
            case 'veneer_bosh_releaseALL_index':
                return [
                    (new Link('boshio_docs_createrelease'))
                        ->setTopic(Link::TOPIC_DOCUMENTATION)
                        ->setTitle('bosh.io')
                        ->setNote('creating a release')
                        ->setUrl('https://bosh.io/docs/create-release.html'),
                    (new Link('boshio_releases'))
                        ->setTopic(Link::TOPIC_OTHER)
                        ->setTitle('bosh.io')
                        ->setNote('public releases')
                        ->setUrl('https://bosh.io/releases'),
                ];
            case 'veneer_bosh_stemcellALL_index':
                return [
                    (new Link('boshio_docs_stemcell'))
                        ->setTopic(Link::TOPIC_DOCUMENTATION)
                        ->setTitle('bosh.io')
                        ->setNote('about stemcells')
                        ->setUrl('https://bosh.io/docs/stemcell.html'),
                    (new Link('boshio_stemcells'))
                        ->setTopic(Link::TOPIC_OTHER)
                        ->setTitle('bosh.io')
                        ->setNote('public stemcells')
                        ->setUrl('https://bosh.io/stemcells'),
                ];
        }

        return [];
    }
}

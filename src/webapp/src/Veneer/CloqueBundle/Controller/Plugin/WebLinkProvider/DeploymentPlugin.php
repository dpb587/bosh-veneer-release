<?php

namespace Veneer\CloqueBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\WebBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\WebBundle\Plugin\LinkProvider\Link;
use Veneer\Component\Versioning\Repository\RepositoryInterface;
use Veneer\Component\Versioning\WebService\WebServiceInterface;

class DeploymentPlugin implements PluginInterface
{
    protected $versioningRepository;
    protected $versioningWebService;
    protected $directorName;

    public function __construct(
        RepositoryInterface $versioningRepository,
        WebServiceInterface $versioningWebService,
        $directorName
    ) {
        $this->versioningRepository = $versioningRepository;
        $this->versioningWebService = $versioningWebService;
        $this->directorName = $directorName;
    }

    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        return [
            (new Link('cloque_changelog'))
                ->setTopic(Link::TOPIC_CONFIG)
                ->setTitle('Changelog')
                ->setRoute(
                    'veneer_cloque_deployment_changelogALL_index',
                    [
                        'deployment' => $_bosh['deployment']['name'],
                    ]
                ),
            (new Link('cloque_manifestws'))
                ->setTopic(Link::TOPIC_CONFIG)
                ->setTitle('Manifest')
                ->setNote('on GitHub')
                ->setUrl(
                    $this->versioningWebService->getBlobLink(
                        $this->versioningRepository->getFullPath(
                            $this->directorName . '/' . $_bosh['deployment']['name'] . '/bosh.yml'
                        )
                    )
                ),
        ];
    }
}

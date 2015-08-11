<?php

namespace Bosh\CloqueBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\Expr;
use Bosh\CoreBundle\Entity\Deployments;
use Bosh\CoreBundle\Entity\Vms;
use Bosh\CoreBundle\Entity\Releases;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Elastica\Client;
use Bosh\CoreBundle\Service\Plugin\PluginInterface;
use Bosh\VersioningBundle\Service\Repository\RepositoryInterface;
use Bosh\VersioningBundle\Service\WebService\WebServiceInterface;

class DefaultPlugin implements PluginInterface
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

    public function getEndpoints($contextName, array $context = [])
    {
        return [];
    }

    public function getUserPrimaryLinks($contextName, array $context = [])
    {
        switch ($contextName) {
            case 'bosh/deployment':
                return [
                    'cloque_infra' => [
                        'title' => 'infra',
                        'priority' => null,
                        'route' => [
                            'bosh_cloque_deployment_infra_index',
                            [
                                'deployment' => $context['deployment']['name'],
                            ],
                        ],
                    ],
                ];
            default:
                return [];
        }
    }

    public function getUserReferenceLinks($contextName, array $context = [])
    {
        switch ($contextName) {
            case 'bosh/deployment':
                return [
                    'cloque_changelog' => [
                        'topic' => 'config',
                        'title' => 'Changelog',
                        'priority' => 100,
                        'route' => [
                            'bosh_cloque_deployment_changelogALL_index',
                            [
                                'deployment' => $context['deployment']['name'],
                            ],
                        ],
                    ],
                    'cloque_manifestws' => [
                        'topic' => 'config',
                        'title' => 'Manifest',
                        'note' => 'on GitHub',
                        'priority' => 500,
                        'url' => $this->versioningWebService->getBlobLink(
                            $this->versioningRepository->getFullPath(
                                $this->directorName . '/' . $context['deployment']['name'] . '/bosh.yml'
                            )
                        ),
                    ],
                ];
            default:
                return [];
        }
    }

    public function getContext(Request $request, $contextName)
    {
        return [];
    }
}

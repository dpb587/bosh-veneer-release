<?php

namespace Veneer\CloqueBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\Expr;
use Veneer\BoshBundle\Entity\Deployments;
use Veneer\BoshBundle\Entity\Vms;
use Veneer\BoshBundle\Entity\Releases;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Elastica\Client;
use Veneer\BoshBundle\Service\Plugin\PluginInterface;
use Veneer\Component\Versioning\Repository\RepositoryInterface;
use Veneer\Component\Versioning\WebService\WebServiceInterface;

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
                            'veneer_cloque_deployment_infra_index',
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
                            'veneer_cloque_deployment_changelogALL_index',
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

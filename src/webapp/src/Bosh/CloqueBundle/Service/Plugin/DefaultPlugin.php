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

    public function __construct(RepositoryInterface $versioningRepository, WebServiceInterface $versioningWebService)
    {
        $this->versioningRepository = $versioningRepository;
        $this->versioningWebService = $versioningWebService;
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
                    'cloque_changelog' => [
                        'title' => 'changelog',
                        'route' => [
                            'bosh_cloque_deployment_changelogALL_index',
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
        return [];
    }

    public function getContext(Request $request, $contextName)
    {
        return [];
    }
}

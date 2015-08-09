<?php

namespace Bosh\LogsearchBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\Expr;
use Bosh\CoreBundle\Entity\Deployments;
use Bosh\CoreBundle\Entity\Vms;
use Bosh\CoreBundle\Entity\Releases;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Elastica\Client;
use Bosh\CoreBundle\Service\Plugin\PluginInterface;

class DefaultPlugin implements PluginInterface
{
    protected $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function getEndpoints($contextName, array $context = [])
    {
        switch ($contextName) {
            case 'bosh/deployment':
                return [
                    'summarystats' => [
                        'bosh_logsearch_deployment_hoststats',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                ];
            case 'bosh/deployment/instance':
                return [
                    'diskstats' => [
                        'bosh_logsearch_deployment_instance_diskstats',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                    'loadstats' => [
                        'bosh_logsearch_deployment_instance_loadstats',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                    'memstats' => [
                        'bosh_logsearch_deployment_instance_memstats',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                ];
            case 'bosh/deployment/vm/network':
                return [
                    'hoststats' => [
                        'bosh_logsearch_deployment_vm_network_hoststats',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                            'network' => $context['network']['name'],
                        ],
                    ],
                ];
            default:
                return [];
        }
    }

    public function getUserPrimaryLinks($contextName, array $context = [])
    {
        return [];
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

<?php

namespace Veneer\LogsearchBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\Expr;
use Veneer\BoshBundle\Entity\Deployments;
use Veneer\BoshBundle\Entity\Vms;
use Veneer\BoshBundle\Entity\Releases;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Elastica\Client;
use Veneer\BoshBundle\Service\Plugin\PluginInterface;

class DefaultPlugin implements PluginInterface
{
    protected $elasticsearch;
    protected $kibanaUrl;
    protected $directorName;

    public function __construct(Client $elasticsearch, $kibanaUrl, $directorName)
    {
        $this->elasticsearch = $elasticsearch;
        $this->kibanaUrl = $kibanaUrl;
        $this->directorName = $directorName;
    }

    public function getEndpoints($contextName, array $context = [])
    {
        switch ($contextName) {
            case 'bosh/deployment':
                return [
                    'logsearch_monitstatus' => [
                        'veneer_logsearch_deployment_monitstatus',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                ];
            case 'bosh/deployment/instance':
                return [
                    'diskstats' => [
                        'veneer_logsearch_deployment_instance_diskstats',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                    'loadstats' => [
                        'veneer_logsearch_deployment_instance_loadstats',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                    'memstats' => [
                        'veneer_logsearch_deployment_instance_memstats',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                ];
            case 'bosh/deployment/instance/persistent_disk':
                return [
                    'hoststats' => [
                        'veneer_logsearch_deployment_instance_persistentdisk_hoststats',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                            'persistent_disk' => $context['persistent_disk']['id'],
                        ],
                    ],
                ];
            case 'bosh/deployment/vm/network':
                return [
                    'hoststats' => [
                        'veneer_logsearch_deployment_vm_network_hoststats',
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
        switch ($contextName) {
            case 'bosh/deployment/instance':
                return [
                    'kibanametricsjob' => [
                        'topic' => 'performance',
                        'title' => 'Host Stats',
                        'note' => 'Kibana',
                        'url' => sprintf(
                            '%s#/dashboard/elasticsearch/job-metrics?director=%s&deployment=%s&job=%s',
                            $this->kibanaUrl,
                            rawurlencode($this->directorName),
                            rawurlencode($context['deployment']['name']),
                            rawurlencode($context['instance']['job'] . '/' . $context['instance']['index'])
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

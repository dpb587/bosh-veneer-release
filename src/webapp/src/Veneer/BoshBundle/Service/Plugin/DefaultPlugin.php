<?php

namespace Veneer\BoshBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Veneer\BoshBundle\Entity\Deployments;
use Veneer\BoshBundle\Entity\Instances;
use Veneer\BoshBundle\Entity\Vms;
use Veneer\BoshBundle\Entity\Releases;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultPlugin implements PluginInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEndpoints($contextName, array $context = [])
    {
        switch ($contextName) {
            case 'bosh/deployment':
                return [
                    'manifest' => [
                        'veneer_bosh_deployment_manifest',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                    'releases' => [
                        'veneer_bosh_deployment_releases',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                    'stemcells' => [
                        'veneer_bosh_deployment_stemcells',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                    'instanceALL' => [
                        'veneer_bosh_deployment_instanceALL_index',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                    'vmALL' => [
                        'veneer_bosh_deployment_vmALL_index',
                        [
                            'deployment' => $context['deployment']['name'],
                        ]
                    ],
                ];
            case 'bosh/deployment/instance':
                return [
                    'vm' => [
                        'veneer_bosh_deployment_instance_vm',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                    'persistentdiskALL' => [
                        'veneer_bosh_deployment_instance_persistentdiskALL_index',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                ];
            case 'bosh/deployment/instance/persistent_disk':
                return [
                    'cpi' => [
                        'veneer_bosh_deployment_instance_persistentdisk_cpi',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                            'persistent_disk' => $context['persistent_disk']['id'],
                        ],
                    ],
                ];
            case 'bosh/deployment/vm':
                return [
                    'applyspec' => [
                        'veneer_bosh_deployment_vm_applyspec',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                    'instance' => [
                        'veneer_bosh_deployment_vm_instance',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                    'packages' => [
                        'veneer_bosh_deployment_vm_packages',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                    'templates' => [
                        'veneer_bosh_deployment_vm_templates',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                    'networkALL' => [
                        'veneer_bosh_deployment_vm_networkALL_index',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                    'resourcepool_cpi' => [
                        'veneer_bosh_deployment_vm_resourcepool_cpi',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                ];
            case 'bosh/deployment/vm/network':
                return [
                    'cpi' => [
                        'veneer_bosh_deployment_vm_network_cpi',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                            'network' => $context['network']['name'],
                        ],
                    ],
                ];
            case 'bosh/task':
                return [
                    'tracker' => [
                        'veneer_bosh_task_tracker',
                        [
                            'task' => $context['task']['id'],
                        ]
                    ],
                ];
            case 'bosh/release':
                return [
                    'packageALL' => [
                        'veneer_bosh_release_packageALL_index',
                        [
                            'release' => $context['release']['name'],
                        ]
                    ],
                    'versionALL' => [
                        'veneer_bosh_release_versionALL_index',
                        [
                            'release' => $context['release']['name'],
                        ],
                    ],
                ];
            case 'bosh/release/template':
                return [
                    'properties' => [
                        'veneer_bosh_release_template_properties',
                        [
                            'release' => $context['release']['name'],
                            'template' => $context['template']['name'],
                            'version' => $context['template']['version'],
                        ],
                    ],
                ];
            case 'bosh/release/version':
                return [
                    'deployments' => [
                        'veneer_bosh_release_version_deployments',
                        [
                            'release' => $context['release']['name'],
                            'version' => $context['version']['version'],
                        ],
                    ],
                    'packages' => [
                        'veneer_bosh_release_version_packages',
                        [
                            'release' => $context['release']['name'],
                            'version' => $context['version']['version'],
                        ],
                    ],
                    'templates' => [
                        'veneer_bosh_release_version_templates',
                        [
                            'release' => $context['release']['name'],
                            'version' => $context['version']['version'],
                        ],
                    ],
                    'properties' => [
                        'veneer_bosh_release_version_properties',
                        [
                            'release' => $context['release']['name'],
                            'version' => $context['version']['version'],
                        ],
                    ],
                ];
            default:
                return [];
        }
    }

    public function getUserPrimaryLinks($contextName, array $context = [])
    {
        if (preg_match('#^bosh/deployment(/|$)#', $contextName)) {
            return [
                'core_bosh' => [
                    'title' => 'bosh',
                    'priority' => 0,
                    'route' => [
                        'veneer_bosh_deployment_summary',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                ],
            ];
        } elseif (preg_match('#^bosh/release(/|$)#', $contextName)) {
            return [
                'core_bosh' => [
                    'title' => 'bosh',
                    'priority' => 0,
                    'route' => [
                        'veneer_bosh_release_summary',
                        [
                            'release' => $context['release']['name'],
                        ],
                    ],
                ],
            ];
        }

        return [];
    }

    public function getUserReferenceLinks($contextName, array $context = [])
    {
        switch ($contextName) {
            case 'bosh/deployment:all':
                return [
                    'boshio_deploymentmanifestdoc' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_DOCUMENTATION,
                        'title' => 'bosh.io',
                        'note' => 'creating manifests',
                        'url' => 'http://bosh.io/docs/deployment-manifest.html',
                    ],
                ];
            case 'bosh/release:all':
                return [
                    'boshio_createreleasedoc' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_DOCUMENTATION,
                        'title' => 'bosh.io',
                        'note' => 'creating a release',
                        'url' => 'https://bosh.io/docs/create-release.html',
                    ],
                    'boshio_releases' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_OTHER,
                        'title' => 'bosh.io',
                        'note' => 'public releases',
                        'url' => 'https://bosh.io/releases',
                    ],
                ];
            case 'bosh/stemcell:all':
                return [
                    'boshio_aboutstemcellsdoc' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_DOCUMENTATION,
                        'title' => 'bosh.io',
                        'note' => 'about stemcells',
                        'url' => 'https://bosh.io/docs/stemcell.html',
                    ],
                    'boshio_stemcells' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_OTHER,
                        'title' => 'bosh.io',
                        'note' => 'public stemcells',
                        'url' => 'https://bosh.io/stemcells',
                    ],
                ];
            default:
                return [];
        }
    }

    public function getContext(Request $request, $contextName)
    {
        $contextNameSplit = explode('/', $contextName);
        $contextNameSplit[] = null;
        $contextNameSplit[] = null;
        $contextNameSplit[] = null;
        $contextNameSplit[] = null;

        $context = [];

        if ('bosh' == $contextNameSplit[0]) {
            if ('deployment' == $contextNameSplit[1]) {
                $context['deployment'] = $this->loadDeployment(
                    $request->attributes->get('deployment')
                );

                if ('instance' == $contextNameSplit[2]) {
                    $context['instance'] = $this->loadDeploymentInstance(
                        $context['deployment'],
                        $request->attributes->get('job_name'),
                        $request->attributes->get('job_index')
                    );

                    if ('persistent_disk' == $contextNameSplit[3]) {
                        $context['persistent_disk'] = $this->loadDeploymentInstancePersistentDisk(
                            $context['deployment'],
                            $context['instance'],
                            $request->attributes->get('persistent_disk')
                        );
                    }
                } elseif ('vm' == $contextNameSplit[2]) {
                    $context['vm'] = $this->loadDeploymentVm(
                        $context['deployment'],
                        $request->attributes->get('agent')
                    );

                    if ('network' == $contextNameSplit[3]) {
                        $context['network'] = $this->loadDeploymentVmNetwork(
                            $context['vm'],
                            $request->attributes->get('network')
                        );
                    }
                }
            } elseif ('release' == $contextNameSplit[1]) {
                $context['release'] = $this->loadRelease(
                    $request->attributes->get('release')
                );

                if ('version' == $contextNameSplit[2]) {
                    $context['version'] = $this->loadReleaseVersion(
                        $context['release'],
                        $request->attributes->get('version')
                    );
                } elseif ('template' == $contextNameSplit[2]) {
                    $context['template'] = $this->loadReleaseTemplate(
                        $context['release'],
                        $request->attributes->get('template'),
                        $request->attributes->get('version')
                    );
                } elseif ('package' == $contextNameSplit[2]) {
                    $context['package'] = $this->loadReleasePackage(
                        $context['release'],
                        $request->attributes->get('package'),
                        $request->attributes->get('version')
                    );
                }
            } elseif ('task' == $contextNameSplit[1]) {
                $context['task'] = $this->loadTask(
                    $request->attributes->get('task')
                );
            }
        }

        return $context;
    }

    protected function loadTask($task)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Tasks')
            ->findOneById($task);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find task');
        }

        return $loaded;
    }

    protected function loadDeployment($deployment)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Deployments')
            ->findOneByName($deployment);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment');
        }

        return $loaded;
    }

    protected function loadDeploymentInstance(Deployments $deployment, $jobName, $jobIndex)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Instances')
            ->findOneBy([
                'deployment' => $deployment,
                'job' => $jobName,
                'index' => $jobIndex,
            ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment instance');
        }

        return $loaded;
    }

    protected function loadDeploymentInstancePersistentDisk(Deployments $deployment, Instances $instance, $persistentDisk)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:PersistentDisks')
            ->findOneBy([
                'instance' => $instance,
                'id' => $persistentDisk,
            ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment instance persistent disk');
        }

        return $loaded;
    }

    protected function loadDeploymentVm(Deployments $deployment, $agent)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Vms')
            ->findOneBy([
                'deployment' => $deployment,
                'agentId' => $agent,
            ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment vm');
        }

        return $loaded;
    }

    protected function loadDeploymentVmNetwork(Vms $vm, $network)
    {
        if (!isset($vm['applySpecJsonAsArray']['networks'][$network])) {
            throw new NotFoundHttpException('Failed to find deployment vm network');
        }

        $loaded = $vm['applySpecJsonAsArray']['networks'][$network];
        $loaded['name'] = $network;

        return $loaded;
    }
    
    protected function loadRelease($release)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Releases')
            ->createQueryBuilder('r')
            ->andWhere(new Expr\Comparison('r.name', '=', ':release'))->setParameter('release', $release)
            ->getQuery()
            ->getSingleResult();
        
        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find release');
        }
        
        return $loaded;
    }

    protected function loadReleaseVersion(Releases $release, $version)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:ReleaseVersions')
            ->createQueryBuilder('v')
            ->andWhere(new Expr\Comparison('v.release', '=', ':release'))->setParameter('release', $release)
            ->andWhere(new Expr\Comparison('v.version', '=', ':version'))->setParameter('version', $version)
            ->getQuery()
            ->getSingleResult();

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find release version');
        }

        return $loaded;
    }

    protected function loadReleaseTemplate(Releases $release, $template, $version)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Templates')
            ->createQueryBuilder('t')
            ->andWhere(new Expr\Comparison('t.release', '=', ':release'))->setParameter('release', $release)
            ->andWhere(new Expr\Comparison('t.name', '=', ':name'))->setParameter('name', $template)
            ->andWhere(new Expr\Comparison('t.version', '=', ':version'))->setParameter('version', $version)
            ->getQuery()
            ->getSingleResult();

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find release package');
        }

        return $loaded;
    }

    protected function loadReleasePackage(Releases $release, $package, $version)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Packages')
            ->createQueryBuilder('p')
            ->andWhere(new Expr\Comparison('p.release', '=', ':release'))->setParameter('release', $release)
            ->andWhere(new Expr\Comparison('p.name', '=', ':name'))->setParameter('name', $package)
            ->andWhere(new Expr\Comparison('p.version', '=', ':version'))->setParameter('version', $version)
            ->getQuery()
            ->getSingleResult();

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find release package');
        }

        return $loaded;
    }
}

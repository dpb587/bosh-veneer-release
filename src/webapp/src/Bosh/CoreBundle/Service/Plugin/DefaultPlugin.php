<?php

namespace Bosh\CoreBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Bosh\CoreBundle\Entity\Deployments;
use Bosh\CoreBundle\Entity\Vms;
use Bosh\CoreBundle\Entity\Releases;
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
                        'bosh_core_deployment_manifest',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                    'releases' => [
                        'bosh_core_deployment_releases',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                    'stemcells' => [
                        'bosh_core_deployment_stemcells',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                    'instanceALL' => [
                        'bosh_core_deployment_instanceALL_index',
                        [
                            'deployment' => $context['deployment']['name'],
                        ],
                    ],
                    'vmALL' => [
                        'bosh_core_deployment_vmALL_index',
                        [
                            'deployment' => $context['deployment']['name'],
                        ]
                    ],
                ];
            case 'bosh/deployment/instance':
                return [
                    'vm' => [
                        'bosh_core_deployment_instance_vm',
                        [
                            'deployment' => $context['deployment']['name'],
                            'job_name' => $context['instance']['job'],
                            'job_index' => $context['instance']['index'],
                        ],
                    ],
                ];
            case 'bosh/deployment/vm':
                return [
                    'applyspec' => [
                        'bosh_core_deployment_vm_applyspec',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                    'packages' => [
                        'bosh_core_deployment_vm_packages',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                    'templates' => [
                        'bosh_core_deployment_vm_templates',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                    'networkALL' => [
                        'bosh_core_deployment_vm_networkALL_index',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                        ],
                    ],
                ];
            case 'bosh/deployment/vm/network':
                return [
                    'cpi' => [
                        'bosh_core_deployment_vm_network_cpi',
                        [
                            'deployment' => $context['deployment']['name'],
                            'agent' => $context['vm']['agentId'],
                            'network' => $context['network']['name'],
                        ],
                    ],
                ];
            case 'bosh/release':
                return [
                    'packageALL' => [
                        'bosh_core_release_packageALL_index',
                        [
                            'release' => $context['release']['name'],
                        ]
                    ],
                    'versionALL' => [
                        'bosh_core_release_versionALL_index',
                        [
                            'release' => $context['release']['name'],
                        ],
                    ],
                ];
            case 'bosh/release/template':
                return [
                    'properties' => [
                        'bosh_core_release_template_properties',
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
                        'bosh_core_release_version_deployments',
                        [
                            'release' => $context['release']['name'],
                            'version' => $context['version']['version'],
                        ],
                    ],
                    'packages' => [
                        'bosh_core_release_version_packages',
                        [
                            'release' => $context['release']['name'],
                            'version' => $context['version']['version'],
                        ],
                    ],
                    'templates' => [
                        'bosh_core_release_version_templates',
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
        return [];
    }

    public function getUserReferenceLinks($contextName, array $context = [])
    {
        return [];
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
            }
        }

        return $context;
    }

    protected function loadDeployment($deployment)
    {
        $loaded = $this->em->getRepository('BoshCoreBundle:Deployments')
            ->findOneByName($deployment);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment');
        }

        return $loaded;
    }

    protected function loadDeploymentInstance(Deployments $deployment, $jobName, $jobIndex)
    {
        $loaded = $this->em->getRepository('BoshCoreBundle:Instances')
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

    protected function loadDeploymentVm(Deployments $deployment, $agent)
    {
        $loaded = $this->em->getRepository('BoshCoreBundle:Vms')
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
        $loaded = $this->em->getRepository('BoshCoreBundle:Releases')
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
        $loaded = $this->em->getRepository('BoshCoreBundle:ReleaseVersions')
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
        $loaded = $this->em->getRepository('BoshCoreBundle:Templates')
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
        $loaded = $this->em->getRepository('BoshCoreBundle:Packages')
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

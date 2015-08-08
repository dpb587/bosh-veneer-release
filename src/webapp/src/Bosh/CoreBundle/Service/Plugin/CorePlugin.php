<?php

namespace Bosh\CoreBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Bosh\CoreBundle\Entity\Deployments;
use Bosh\CoreBundle\Entity\Vms;
use Bosh\CoreBundle\Entity\Releases;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CorePlugin implements PluginInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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

}

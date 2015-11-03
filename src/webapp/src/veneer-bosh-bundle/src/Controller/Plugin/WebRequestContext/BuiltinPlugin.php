<?php

namespace Veneer\BoshBundle\Controller\Plugin\WebRequestContext;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\Request;
use Veneer\BoshBundle\Entity\Deployments;
use Veneer\BoshBundle\Entity\Instances;
use Veneer\BoshBundle\Entity\Vms;
use Veneer\BoshBundle\Entity\Releases;
use Veneer\CoreBundle\Plugin\RequestContext\PluginInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BuiltinPlugin implements PluginInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function applyContext(Request $request, $context)
    {
        $contextSplit = explode('/', $context);
        $contextSplit[] = null;
        $contextSplit[] = null;
        $contextSplit[] = null;
        $contextSplit[] = null;

        $veneerBoshContext = [];

        if ('bosh' == $contextSplit[0]) {
            if ('deployment' == $contextSplit[1]) {
                $veneerBoshContext['deployment'] = $this->loadDeployment(
                    $request->attributes->get('deployment')
                );

                if ('job' == $contextSplit[2]) {
                    $veneerBoshContext['job'] = $this->loadDeploymentJob(
                        $veneerBoshContext['deployment'],
                        $request->attributes->get('job')
                    );

                    if ('index' == $contextSplit[3]) {
                        $veneerBoshContext['index'] = $this->loadDeploymentJobIndex(
                            $veneerBoshContext['deployment'],
                            $request->attributes->get('job'),
                            $request->attributes->get('index')
                        );

                        if ('persistent_disk' == $contextSplit[4]) {
                            $veneerBoshContext['persistent_disk'] = $this->loadDeploymentJobIndexPersistentDisk(
                                $veneerBoshContext['deployment'],
                                $veneerBoshContext['index'],
                                $request->attributes->get('persistent_disk')
                            );
                        }
                    }
                } elseif ('vm' == $contextSplit[2]) {
                    $veneerBoshContext['vm'] = $this->loadDeploymentVm(
                        $veneerBoshContext['deployment'],
                        $request->attributes->get('agent')
                    );

                    if ('network' == $contextSplit[3]) {
                        $veneerBoshContext['network'] = $this->loadDeploymentVmNetwork(
                            $veneerBoshContext['vm'],
                            $request->attributes->get('network')
                        );
                    }
                }
            } elseif ('release' == $contextSplit[1]) {
                $veneerBoshContext['release'] = $this->loadRelease(
                    $request->attributes->get('release')
                );

                if ('version' == $contextSplit[2]) {
                    $veneerBoshContext['version'] = $this->loadReleaseVersion(
                        $veneerBoshContext['release'],
                        $request->attributes->get('version')
                    );
                } elseif ('template' == $contextSplit[2]) {
                    $veneerBoshContext['template'] = $this->loadReleaseTemplate(
                        $veneerBoshContext['release'],
                        $request->attributes->get('template'),
                        $request->attributes->get('version')
                    );
                } elseif ('package' == $contextSplit[2]) {
                    $veneerBoshContext['package'] = $this->loadReleasePackage(
                        $veneerBoshContext['release'],
                        $request->attributes->get('package'),
                        $request->attributes->get('version')
                    );
                }
            } elseif ('task' == $contextSplit[1]) {
                $veneerBoshContext['task'] = $this->loadTask(
                    $request->attributes->get('task')
                );
            }
        }

        $request->attributes->set('_bosh', $veneerBoshContext);
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

    protected function loadDeploymentJob(Deployments $deployment, $jobName)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Instances')
            ->findOneBy([
                'deployment' => $deployment,
                'job' => $jobName,
            ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment job');
        }

        return [
            'job' => $jobName,
        ];
    }

    protected function loadDeploymentJobIndex(Deployments $deployment, $jobName, $jobIndex)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Instances')
            ->findOneBy([
                'deployment' => $deployment,
                'job' => $jobName,
                'index' => $jobIndex,
            ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment job index');
        }

        return $loaded;
    }

    protected function loadDeploymentJobIndexPersistentDisk(Deployments $deployment, Instances $instance, $persistentDisk)
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

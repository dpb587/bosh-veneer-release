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

                if ('instance_group' == $contextSplit[2]) {
                    $veneerBoshContext['instance_group'] = $this->loadDeploymentInstanceGroup(
                        $veneerBoshContext['deployment'],
                        $request->attributes->get('instance_group')
                    );

                    if ('instance' == $contextSplit[3]) {
                        $veneerBoshContext['instance'] = $this->loadDeploymentInstanceGroupInstance(
                            $veneerBoshContext['deployment'],
                            $request->attributes->get('instance_group'),
                            $request->attributes->get('instance')
                        );

                        if ('persistent_disk' == $contextSplit[4]) {
                            $veneerBoshContext['persistent_disk'] = $this->loadDeploymentInstanceGroupInstancePersistentDisk(
                                $veneerBoshContext['deployment'],
                                $veneerBoshContext['instance'],
                                $request->attributes->get('persistent_disk')
                            );
                        } elseif ('network' == $contextSplit[4]) {
                            $veneerBoshContext['network'] = $this->loadDeploymentInstanceGroupInstanceNetwork(
                                $veneerBoshContext['instance'],
                                $request->attributes->get('network')
                            );
                        }
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
            } elseif ('event' == $contextSplit[1]) {
                $veneerBoshContext['event'] = $this->loadEvent(
                    $request->attributes->get('event')
                );
            } elseif ('cloud-config' == $contextSplit[1]) {
                $veneerBoshContext['cloudconfig'] = $this->em->getRepository('VeneerBoshBundle:CloudConfigs')
                    ->findOneBy([], ['id' => 'desc']);
            }
        }

        $request->attributes->set('_bosh', $veneerBoshContext);
    }

    protected function loadEvent($event)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Events')
            ->findOneById($event);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find event');
        }

        return $loaded;
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

    protected function loadDeploymentInstanceGroup(Deployments $deployment, $jobName)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Instances')
            ->findOneBy([
                'deployment' => $deployment,
                'job' => $jobName,
            ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment instance group');
        }

        return [
            'job' => $jobName,
        ];
    }

    protected function loadDeploymentInstanceGroupInstance(Deployments $deployment, $instanceGroupName, $instanceGroupInstance)
    {
        $loaded = $this->em->getRepository('VeneerBoshBundle:Instances')
            ->findOneBy([
                'deployment' => $deployment,
                'job' => $instanceGroupName,
                'uuid' => $instanceGroupInstance,
            ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find deployment instance group instance');
        }

        return $loaded;
    }

    protected function loadDeploymentInstanceGroupInstancePersistentDisk(Deployments $deployment, Instances $instance, $persistentDisk)
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

    protected function loadDeploymentInstanceGroupInstanceNetwork(Instances $instance, $network)
    {
        if (!isset($instance['specJsonAsArray']['networks'][$network])) {
            throw new NotFoundHttpException('Failed to find deployment instance group instance network');
        }

        $loaded = $instance['specJsonAsArray']['networks'][$network];
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

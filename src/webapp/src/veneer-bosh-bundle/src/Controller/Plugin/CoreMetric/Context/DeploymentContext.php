<?php

namespace Veneer\BoshBundle\Controller\Plugin\CoreMetric\Context;

use Veneer\CoreBundle\Plugin\Metric\Context\SimpleContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class DeploymentContext extends SimpleContext
{
    protected $container;
    protected $em;

    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        parent::__construct($container);

        $this->container = $container;
        $this->em = $em;
    }

    public function resolve($name)
    {
        if (preg_match('/^job\[([^]]+)\]$/', $name, $match)) {
            $entity = $this->em->getRepository('VeneerBoshBundle:Instances')->findOneBy([
                'deployment' => $this->context['deployment'],
                'job' => $match[1],
            ]);

            if (!$entity) {
                throw new \InvalidArgumentException('Invalid job key');
            }

            $context = $this->container->get('veneer_bosh.plugin.core_metric.context.deployment_instancegroup');
            $context->replaceContext($this->context);
            $context->addContext('job', ['job' => $entity['job']]);

            return $context;
        } elseif (preg_match('/^vm\[([^]]+)\]$/', $name, $match)) {
            $entity = $this->em->getRepository('VeneerBoshBundle:Vms')->findOneBy([
                'deployment' => $this->context['deployment'],
                'agentId' => $match[1],
            ]);

            if (!$entity) {
                throw new \InvalidArgumentException('Invalid vm key');
            }

            $context = $this->container->get('veneer_bosh.plugin.core_metric.context.deployment_instancegroup_instance');
            $context->replaceContext($this->context);
            $context->addContext('vm', $entity);

            return $context;
        }

        return parent::resolve($name);
    }
}

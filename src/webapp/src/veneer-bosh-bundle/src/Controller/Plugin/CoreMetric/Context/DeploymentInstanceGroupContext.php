<?php

namespace Veneer\BoshBundle\Controller\Plugin\CoreMetric\Context;

use Veneer\CoreBundle\Plugin\Metric\Context\SimpleContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class DeploymentInstanceGroupContext extends SimpleContext
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
        if (preg_match('/^instance\[([^]]+)\]$/', $name, $match)) {
            $entity = $this->em->getRepository('VeneerBoshBundle:Instances')->findOneBy([
                'job' => $this->context['job']['job'],
                'uuid' => $match[1],
            ]);

            if (!$entity) {
                throw new \InvalidArgumentException('Invalid index key');
            }

            $context = $this->container->get('veneer_bosh.plugin.core_metric.context.deployment_instancegroup_instance');
            $context->replaceContext($this->context);
            $context->addContext('job_index', $entity);

            return $context;
        }

        return parent::resolve($name);
    }
}

<?php

namespace Veneer\BoshBundle\Controller\Plugin\CoreMetric\Context;

use Veneer\CoreBundle\Plugin\Metric\Context\SimpleContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class DeploymentInstanceGroupInstanceContext extends SimpleContext
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
        if (preg_match('/^persistent_disk\[([^]]+)\]$/', $name, $match)) {
            $entity = $this->em->getRepository('VeneerBoshBundle:PersistentDisks')->findOneBy([
                #'instance' => $this->context['job_index'],
                'id' => $match[1],
            ]);

            if (!$entity) {
                throw new \InvalidArgumentException('Invalid persistent_disk key');
            }

            $context = $this->container->get('veneer_bosh.plugin.core_metric.context.deployment_job_index_persistent_disk');
            $context->replaceContext($this->context);
            $context->addContext('persistent_disk', $entity);

            return $context;
        } elseif ('vm' == $name) {
            if (!$this->context['job_index']['vm']) {
                throw new \InvalidArgumentException('No vm exists');
            }

            $context = $this->container->get('veneer_bosh.plugin.core_metric.context.deployment_vm');
            $context->replaceContext($this->context);
            $context->addContext('vm', $this->context['job_index']['vm']);

            return $context;
        }

        return parent::resolve($name);
    }
}

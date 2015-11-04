<?php

namespace Veneer\BoshBundle\Controller\Plugin\CoreMetric\Context;

use Veneer\CoreBundle\Plugin\Metric\Context\SimpleContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class DeploymentVmContext extends SimpleContext
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
        if (preg_match('/^network\[([^]]+)\]$/', $name, $match)) {
            if (!isset($this->context['vm']['applySpecJsonAsArray']['networks'][$match[1]])) {
                throw new \InvalidArgumentException('Invalid network key');
            }

            $network = $this->context['vm']['applySpecJsonAsArray']['networks'][$match[1]];
            $network['name'] = $match[1];

            $context = $this->container->get('veneer_bosh.plugin.core_metric.context.deployment_vm_network');
            $context->replaceContext($this->context);
            $context->addContext('network', $network);

            return $context;
        }

        return parent::resolve($name);
    }
}

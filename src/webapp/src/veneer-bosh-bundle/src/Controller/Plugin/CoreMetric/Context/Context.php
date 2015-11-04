<?php

namespace Veneer\BoshBundle\Controller\Plugin\CoreMetric\Context;

use Veneer\CoreBundle\Plugin\Metric\Context\SimpleContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class Context extends SimpleContext
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
        if (preg_match('/^deployment\[([^]]+)\]$/', $name, $match)) {
            $entity = $this->em->getRepository('VeneerBoshBundle:Deployments')->findOneBy([
                'name' => $match[1],
            ]);

            $context = $this->container->get('veneer_bosh.plugin.core_metric.context.deployment');
            $context->replaceContext($this->context);
            $context->addContext('deployment', $entity);

            return $context;
        }

        parent::resolve($name);
    }
}

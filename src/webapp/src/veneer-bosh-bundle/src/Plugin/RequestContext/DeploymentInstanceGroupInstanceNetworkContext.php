<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class DeploymentInstanceGroupInstanceNetworkContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, DeploymentInstanceGroupInstanceContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\DeploymentInstanceGroupInstanceNetwork $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getNetworkAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $instance = $context[$annotation->getInstanceAttribute()];

        if (!isset($instance['specJsonAsArray']['networks'][$attributeValue])) {
            throw new NotFoundHttpException('Network not found: %s', $attributeValue);
        }

        $found = $instance['specJsonAsArray']['networks'][$attributeValue];
        $found['name'] = $attributeValue;

        $context[$attributeName] = $found;
    }
}

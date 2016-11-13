<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class DeploymentInstanceGroupInstancePersistentDiskContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, DeploymentInstanceGroupInstanceContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\DeploymentInstanceGroupInstancePersistentDisk $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getPersistentDiskAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerBoshBundle:PersistentDisks')->findOneBy([
            'instance' => $context[$annotation->getInstanceAttribute()],
            'id' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Persistent disk not found: %s', $attributeValue));
        }

        $context[$attributeName] = $found;
    }
}

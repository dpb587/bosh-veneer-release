<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class DeploymentInstanceGroupInstanceContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, DeploymentInstanceGroupContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\DeploymentInstanceGroupInstance $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getInstanceAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerBoshBundle:Instances')->findOneBy([
            'deployment' => $context[$annotation->getDeploymentAttribute()],
            'job' => $context[$annotation->getInstanceGroupAttribute()]['job'],
            'uuid' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Instance not found: %s', $attributeValue));
        }

        $context[$attributeName] = $found;
    }
}

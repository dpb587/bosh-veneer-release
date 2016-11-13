<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class DeploymentInstanceGroupContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, DeploymentContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\DeploymentInstanceGroup $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getInstanceGroupAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerBoshBundle:Instances')->findOneBy([
            'deployment' => $context[$annotation->getDeploymentAttribute()],
            'job' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Instance group not found: %s', $attributeValue));
        }

        $context[$attributeName] = [
            'job' => $attributeValue,
        ];
    }
}

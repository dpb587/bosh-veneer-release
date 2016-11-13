<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class DeploymentContext
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function apply(Request $request, Annotations\Deployment $annotation, Context $context)
    {
        $attributeName = $annotation->getDeploymentAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerBoshBundle:Deployments')->findOneBy([
            'name' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Deployment not found: %s', $attributeValue));
        }

        $context[$attributeName] = $found;
    }
}

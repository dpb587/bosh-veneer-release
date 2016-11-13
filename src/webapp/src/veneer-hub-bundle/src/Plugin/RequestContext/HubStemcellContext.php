<?php

namespace Veneer\HubBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class HubStemcellContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, HubContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\HubStemcell $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getStemcellAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerHubBundle:StemcellVersion')->findOneBy([
            'hub' => $context[$annotation->getHubAttribute()]['name'],
            'stemcell' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Stemcell not found: %s', $attributeValue));
        }

        $context[$attributeName] = [
            'name' => $attributeValue,
        ];
    }
}

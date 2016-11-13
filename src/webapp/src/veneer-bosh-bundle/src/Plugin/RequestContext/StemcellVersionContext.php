<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class StemcellVersionContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, StemcellContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\StemcellVersion $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getVersionAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerBoshBundle:Stemcells')->findOneBy([
            'name' => $context[$annotation->getStemcellAttribute()]['name'],
            'version' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Version not found: %s', $attributeValue));
        }

        $context[$attributeName] = $found;
    }
}

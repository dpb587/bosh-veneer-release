<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class ReleasePackageContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, ReleaseContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\ReleasePackage $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getPackageAttribute();
        $attributeVersionName = $annotation->getVersionAttribute();

        if (isset($context[$attributeName], $context[$attributeVersionName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);
        $attributeVersionValue = $request->attributes->get($attributeVersionName);

        $found = $this->em->getRepository('VeneerBoshBundle:Packages')->findOneBy([
            'release' => $context[$annotation->getReleaseAttribute()],
            'name' => $attributeValue,
            'version' => $attributeVersionValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Package not found: %s/%s', $attributeValue, $attributeVersionValue));
        }

        $context[$attributeName] = $found;
    }
}

<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class ReleaseVersionContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, ReleaseContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\ReleaseVersion $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getVersionAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerBoshBundle:ReleaseVersions')->findOneBy([
            'release' => $context[$annotation->getReleaseAttribute()],
            'version' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Version not found: %s', $attributeValue));
        }

        $context[$attributeName] = $found;
    }
}

<?php

namespace Veneer\HubBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class HubReleaseVersionContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, HubReleaseContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\HubReleaseVersion $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getVersionAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerHubBundle:ReleaseVersion')->findOneBy([
            'hub' => $context[$annotation->getHubAttribute()]['name'],
            'release' => $context[$annotation->getReleaseAttribute()]['name'],
            'version' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Version not found: %s', $attributeValue));
        }

        $context[$attributeName] = $found;
    }
}

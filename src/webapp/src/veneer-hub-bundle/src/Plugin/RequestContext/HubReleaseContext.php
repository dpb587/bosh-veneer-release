<?php

namespace Veneer\HubBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class HubReleaseContext
{
    protected $em;
    protected $parentContext;

    public function __construct(EntityManager $em, HubContext $parentContext)
    {
        $this->em = $em;
        $this->parentContext = $parentContext;
    }

    public function apply(Request $request, Annotations\HubRelease $annotation, Context $context)
    {
        $this->parentContext->apply($request, $annotation, $context);

        $attributeName = $annotation->getReleaseAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerHubBundle:ReleaseVersion')->findOneBy([
            'hub' => $context[$annotation->getHubAttribute()]['name'],
            'release' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Release not found: %s', $attributeValue));
        }

        $context[$attributeName] = [
            'name' => $attributeValue,
        ];
    }
}

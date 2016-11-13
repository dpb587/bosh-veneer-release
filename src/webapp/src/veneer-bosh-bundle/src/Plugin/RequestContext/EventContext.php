<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class EventContext
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function apply(Request $request, Annotations\Event $annotation, Context $context)
    {
        $attributeName = $annotation->getEventAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerBoshBundle:Events')->findOneBy([
            'id' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Event not found: %s', $attributeValue));
        }

        $context[$attributeName] = $found;
    }
}

<?php

namespace Veneer\HubBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\HubBundle\Service\HubFactory;

class HubContext
{
    protected $factory;

    public function __construct(HubFactory $factory)
    {
        $this->factory = $factory;
    }

    public function apply(Request $request, Annotations\Hub $annotation, Context $context)
    {
        $attributeName = $annotation->getHubAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        try {
            $service = $this->factory->get($attributeValue);
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException(sprintf('Hub not found: %s', $attributeValue), $e);
        }

        $context[$attributeName] = [
            'name' => $request->attributes->get('hub'),
            'title' => $service->getTitle(),
            'details' => $service->getDetails(),
        ];
    }
}

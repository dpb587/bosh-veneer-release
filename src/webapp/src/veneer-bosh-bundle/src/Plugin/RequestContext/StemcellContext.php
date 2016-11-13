<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\BoshBundle\Service\StemcellNameParser;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class StemcellContext
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function apply(Request $request, Annotations\Stemcell $annotation, Context $context)
    {
        $attributeName = $annotation->getStemcellAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $attributeValue = $request->attributes->get($attributeName);

        $found = $this->em->getRepository('VeneerBoshBundle:Stemcells')->findOneBy([
            'name' => $attributeValue,
        ]);

        if (!$found) {
            throw new NotFoundHttpException(sprintf('Stemcell not found: %s', $attributeValue));
        }

        try {
            $contextValue = StemcellNameParser::parse($attributeName);
        } catch (\InvalidArgumentException $e) {
            $contextValue = [
                'name' => $attributeValue,
            ];
        }

        $context[$attributeName] = $contextValue;
    }
}

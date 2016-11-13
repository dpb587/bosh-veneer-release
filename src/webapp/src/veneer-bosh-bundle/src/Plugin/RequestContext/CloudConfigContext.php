<?php

namespace Veneer\BoshBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;

class CloudConfigContext
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function apply(Request $request, Annotations\CloudConfig $annotation, Context $context)
    {
        $attributeName = $annotation->getCloudConfigAttribute();

        if (isset($context[$attributeName])) {
            return;
        }

        $found = $this->em->getRepository('VeneerBoshBundle:CloudConfigs')->findOneBy(
            [],
            [
                'id' => 'DESC',
            ]
        );

        if (!$found) {
            throw new NotFoundHttpException('Cloud config not found');
        }

        $context[$attributeName] = $found;
    }
}

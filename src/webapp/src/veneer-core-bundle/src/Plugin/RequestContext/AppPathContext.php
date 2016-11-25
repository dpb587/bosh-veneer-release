<?php

namespace Veneer\CoreBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\CoreBundle\Service\Storage\System;

class AppPathContext
{
    protected $storage;

    public function __construct(System $storage)
    {
        $this->storage = $storage;
    }

    public function apply(Request $request, Annotations\AppPath $annotation, Context $context)
    {
        if (isset($context['app'])) {
            return;
        }

        $pathName = $annotation->getPathAttribute();

        if (!$request->query->has($pathName)) {
            throw new NotFoundHttpException('Query parameter for path is missing');
        }

        $pathValue = $request->query->get($pathName);

        $file = $this->storage->get($pathValue);

        #$draftProfile = $this->repository->getDraftProfile($annotation->name.'-'.substr(md5($pathValue), 0, 8), $pathValue);

        $context['app'] = [
            'file' => $file,
            'profile' => [
                'draft_started' => false,
                'ref_read' => 'main',
            ],
        ];
    }
}

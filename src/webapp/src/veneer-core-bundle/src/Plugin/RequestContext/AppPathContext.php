<?php

namespace Veneer\CoreBundle\Plugin\RequestContext;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;

class AppPathContext
{
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
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

        $draftProfile = $this->repository->getDraftProfile($annotation->name.'-'.substr(md5($pathValue), 0, 8), $pathValue);

        $context['app'] = [
            'file' => $pathValue,
            'profile' => $draftProfile,
        ];
    }
}

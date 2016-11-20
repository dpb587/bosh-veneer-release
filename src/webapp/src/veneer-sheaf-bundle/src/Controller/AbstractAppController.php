<?php

namespace Veneer\SheafBundle\Controller;

use Elastica\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\CoreBundle\Plugin\RequestContext\Annotations as CoreContext;

abstract class AbstractAppController extends AbstractController
{
    protected $repository;
    protected $app;
    protected $installationHash;
    protected $installationHelper;

    public function applyRequestContext(Request $request, Context $context)
    {
        $this->repository = $this->container->get('veneer_core.workspace.repository');
        $this->installationHelper = $this->container->get('veneer_sheaf.installation_helper');

        if ($this->repository->fileExists($context['app']['path'], $context['app']['profile']['ref_read'])) {
            $this->installationHash = Yaml::parse($this->repository->showFile($context['app']['path'], $context['app']['profile']['ref_read'])) ?: [];
        }
    }
}

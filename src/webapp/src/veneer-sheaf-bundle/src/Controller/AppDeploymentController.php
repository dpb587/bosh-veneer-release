<?php

namespace Veneer\SheafBundle\Controller;

use Elastica\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\BoshBundle\Controller\CloudConfigController;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Plugin\RequestContext\Context;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\CoreBundle\Plugin\RequestContext\Annotations as CoreContext;

/**
 * @CoreContext\AppPath(name = "sheaf-install")
 * @CoreContext\ControllerMethod
 */
class AppDeploymentController extends AbstractAppController
{
    protected $deployment;

    public function applyRequestContext(Request $request, Context $context)
    {
        parent::applyRequestContext($request, $context);

        $componentValue = $request->attributes->get('deployment');

        if (!isset($this->installationHash['installation']['components'][$componentValue])) {
            throw new NotFoundHttpException(sprintf('Failed to find component: %s', $componentValue));
        }

        $componentFound = false;
        $component = null;

        foreach ($this->installationHash['components'] as $component) {
            if ($component['name'] == $componentValue) {
                $componentFound = true;
                break;
            }
        }

        if (!$componentFound) {
            throw new NotFoundHttpException(sprintf('Failed to find component: %s', $componentValue));
        } elseif ($component['type'] != 'deployment') {
            throw new NotFoundHttpException(sprintf('Failed to find deployment: %s', $componentValue));
        }

        $physicalCheckout = $this->repository->createCheckout($context['app']['profile']['ref_read'])->getPhysicalCheckout();

        $this->manifestBuilder = $this->container->get('veneer_bosh_editor.manifest_builder.bosh');

        $result = Yaml::parse(
            $this->manifestBuilder->build(
                $physicalCheckout->getPhysicalPath(),
                sprintf(
                    'bosh/deployment/%s-%s/manifest.yml',
                    $this->installationHash['installation']['name'],
                    $component['name']
                )
            )
        );

        die(print_r($this->manifestBuilder->findMissingParameters($result), true));
    }

    public function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return $nav->add(
                $this->installationHash['installation']['name'],
                [
                    'veneer_sheaf_app_summary' => [
                        'path' => $_bosh['app']['path'],
                    ],
                ]
            )
            ;
    }

    public function summaryAction(Context $_bosh)
    {
        return $this->renderApi(
            'VeneerSheafBundle:App:summary.html.twig',
            [
                'installation' => $this->installationHash,
                'logo' => base64_encode($this->container->get('veneer_core.workspace.repository')->showFile(dirname($_bosh['app']['path']).'/logo.png', $_bosh['app']['profile']['ref_read'])),
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $_bosh),
                'sidenav_active' => 'summary',
            ]
        );
    }
}

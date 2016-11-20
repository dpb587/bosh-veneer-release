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
use Veneer\CoreBundle\Service\SchemaMap\DataNode\ArrayDataNode;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\CoreBundle\Plugin\RequestContext\Annotations as CoreContext;

/**
 * @CoreContext\AppPath(name = "sheaf-install")
 * @CoreContext\ControllerMethod
 */
class AppDeploymentController extends AbstractAppController
{
    protected $deployment;
    protected $manifestBuilder;

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

        $this->deployment = $component;
        $this->manifestBuilder = $this->container->get('veneer_bosh_editor.manifest_builder.bosh');
    }

    public function defNav(Breadcrumbs $nav, Context $_bosh)
    {
        return $nav->add(
                $this->installationHash['installation']['name'],
                [
                    'veneer_sheaf_app_summary' => [
                        'file' => $_bosh['app']['file'],
                    ],
                ]
            )
            ;
    }

    public function summaryAction(Context $_bosh)
    {
        $physicalCheckout = $this->repository->createCheckout($_bosh['app']['profile']['ref_read'])->getPhysicalCheckout();

        $result = Yaml::parse(
            $this->manifestBuilder->build(
                $physicalCheckout->getPhysicalPath(),
                sprintf(
                    'bosh/deployment/%s-%s/manifest.yml',
                    $this->installationHash['installation']['name'],
                    $this->deployment['name']
                )
            )
        );

        $params = $this->manifestBuilder->findMissingParameters($result);

        $dataNode = (new ArrayDataNode(''))->setData($result);

        $details = [];

        foreach ($params as $paramKey => $paramUsages) {
            $details[$paramKey] = $this->container->get('veneer_bosh.schema_map.deployment_v2')->traverse($dataNode, $paramUsages[0]);
        }

        $formBuilder = $this->container->get('form.factory')->createNamedBuilder('data');

        foreach ($details as $paramKey => $schemaMapNode) {
            $this->container->get('veneer_core.schema_map.form_builder')->buildForm($formBuilder, $schemaMapNode->getSchema(), $paramKey, []);
        }

        $form = $formBuilder->getForm();

        return $this->renderApi(
            'VeneerSheafBundle:App:edit.html.twig',
            [
                'title' => 'something',
                'form' => $form->createView(),
            ],
            [
                'installation' => $this->installationHash,
                'logo' => base64_encode($this->container->get('veneer_core.workspace.repository')->showFile(dirname($_bosh['app']['file']).'/logo.png', $_bosh['app']['profile']['ref_read'])),
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $_bosh),
                'sidenav_active' => 'summary',
            ]
        );
        return $this->renderApi(
            'VeneerSheafBundle:App:summary.html.twig',
            [
                'installation' => $this->installationHash,
                'logo' => base64_encode($this->container->get('veneer_core.workspace.repository')->showFile(dirname($_bosh['app']['file']).'/logo.png', $_bosh['app']['profile']['ref_read'])),
            ],
            [
                'def_nav' => self::defNav($this->container->get('veneer_sheaf.breadcrumbs'), $_bosh),
                'sidenav_active' => 'summary',
            ]
        );
    }
}
